<?php

namespace App\Controller;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Elmasry\Support\Session;
use Elmasry\Validation\Validator;
use App\Models\InvoiceItem;

class InvoiceController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
        if (!$this->session->has('is_authenticated')) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $invoices = Invoice::allWithUsers();
        return view('invoices.index', ['invoices' => $invoices]);
    }

    public function show($id)
    {
        $invoice = Invoice::findWithUser($id);

        if (!$invoice) {
            $this->session->setFlash('errors', ['general' => ['Invoice not found.']]);
            header('Location: /invoices');
            exit;
        }

        $items = Invoice::getItems($id);

        return view('invoices.show', [
            'invoice' => $invoice,
            'items' => $items
        ]);
    }

    public function create()
    {
        $products = Product::all();
        $users = User::all();

        return view('invoices.create', [
            'products' => $products,
            'users' => $users
        ]);
    }

    public function store()
    {
        $v = new Validator();
        $v->setRules(['user_id' => 'required|numeric']);
        $v->make(request()->all());

        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            return back();
        }

        // Prepare items array
        $products = request()->get('products') ?? [];
        $quantities = request()->get('quantities') ?? [];
        $prices = request()->get('prices') ?? []; // Optional override

        $items = [];
        for ($i = 0; $i < count($products); $i++) {
            if (!empty($products[$i]) && !empty($quantities[$i])) {
                $items[] = [
                    'product_id' => $products[$i],
                    'quantity' => $quantities[$i],
                    // We let the Model recalculate price from DB for security, usually,
                    // but createWithItems in Model currently fetches price from DB. 
                    // However, sometimes custom price is allowed.
                    // The Model's createWithItems implementation currently ignores passed price 
                    // and re-fetches from Product table. If that's intended, good.
                ];
            }
        }

        if (empty($items)) {
            $this->session->setFlash('errors', ['items' => ['Please add at least one item.']]);
            return back();
        }

        try {
            $invoiceId = Invoice::createWithItems(request()->get('user_id'), $items);

            $this->session->setFlash('success', 'Invoice created successfully!');
            header("Location: /invoices/{$invoiceId}");
            exit;

        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to create invoice: ' . $e->getMessage()]]);
            return back();
        }
    }

    public function edit($id)
    {
        $invoice = Invoice::findWithUser($id);

        if (!$invoice) {
            $this->session->setFlash('errors', ['general' => ['Invoice not found.']]);
            header('Location: /invoices');
            exit;
        }

        $items = Invoice::getItems($id);
        $products = Product::all();
        $users = User::all();

        return view('invoices.edit', [
            'invoice' => $invoice,
            'items' => $items,
            'products' => $products,
            'users' => $users
        ]);
    }

    public function update($id)
    {
        $v = new Validator();
        $v->setRules(['user_id' => 'required|numeric']);
        $v->make(request()->all());

        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            return back();
        }

        try {
            Invoice::updateWithItems(
                $id,
                request()->all(),
                request()->get('products') ?? [],
                request()->get('quantities') ?? [],
                request()->get('prices') ?? []
            );

            $this->session->setFlash('success', 'Invoice updated successfully!');
            header("Location: /invoices/{$id}");
            exit;

        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to update invoice: ' . $e->getMessage()]]);
            return back();
        }
    }

    public function destroy($id)
    {
        // For destruction, logic is simple enough to stay here or move to Model::deleteWithItems
        // But since we have foreign keys (hopefully), usually deleting parent is enough if ON DELETE CASCADE.
        // If not, manual delete is needed.
        // Let's assume logic stays for now or we could add Invoice::deleteWithItems($id).
        // The original controller had manual item deletion.
        try {
            $items = Invoice::getItems($id);
            foreach ($items as $item) {
                InvoiceItem::delete($item['id']);
            }
            Invoice::delete($id);

            $this->session->setFlash('success', 'Invoice deleted successfully!');
            header('Location: /invoices');
            exit;

        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to delete invoice.']]);
            return back();
        }
    }
}