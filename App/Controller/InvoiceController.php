<?php
// app/Controllers/InvoiceController.php

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
    }
    
    /**
     * Display all invoices
     */
    public function index()
    {
        $invoices = Invoice::allWithUsers();
        
        return view('invoices.index', ['invoices' => $invoices]);
    }
    
    /**
     * Show single invoice
     */
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
    
    /**
     * Show create form
     */
    public function create()
    {
        $products = Product::all();
        $users = User::all();
        
        return view('invoices.create', [
            'products' => $products,
            'users' => $users
        ]);
    }
    
    /**
     * Store new invoice
     */
    public function store()
    {
        // Validate
        $v = new Validator();
        $v->setRules([
            'user_id' => 'required|numeric'
        ]);
        
        $v->make(request()->all());
        
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            return back();
        }
        
        // Get form arrays
        $products = request()->get('products') ?? [];
        $quantities = request()->get('quantities') ?? [];
        $prices = request()->get('prices') ?? [];
        
        // Build items array
        $items = [];
        for ($i = 0; $i < count($products); $i++) {
            if (!empty($products[$i]) && !empty($quantities[$i])) {
                $items[] = [
                    'product_id' => $products[$i],
                    'quantity' => $quantities[$i],
                    'price' => $prices[$i] ?? 0
                ];
            }
        }
        
        if (empty($items)) {
            $this->session->setFlash('errors', ['items' => ['Please add at least one item.']]);
            return back();
        }
        
        // Create invoice
        try {
            $invoiceId = Invoice::createWithItems(
                request()->get('user_id'),
                $items
            );
            
            $this->session->setFlash('success', 'Invoice created successfully!');
            header("Location: /invoices/{$invoiceId}");
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to create invoice: ' . $e->getMessage()]]);
            return back();
        }
    }
    
    /**
     * Delete invoice
     */
    public function destroy($id)
    {
        try {
            // Delete invoice items first
            $items = Invoice::getItems($id);
            foreach ($items as $item) {
                InvoiceItem::delete($item['id']);
            }
            
            // Then delete invoice
            Invoice::delete($id);
            
            $this->session->setFlash('success', 'Invoice deleted successfully!');
            header('Location: /invoices');
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to delete invoice.']]);
            return back();
        }
    }

    /**
     * Show edit form
     */
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

    /**
     * Update invoice
     */
    public function update($id)
    {
        // Validate
        $v = new Validator();
        $v->setRules([
            'user_id' => 'required|numeric'
        ]);
        
        $v->make(request()->all());
        
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            return back();
        }
        
        try {
            // Get form data
            $products = request()->get('products') ?? [];
            $quantities = request()->get('quantities') ?? [];
            $prices = request()->get('prices') ?? [];
            $paid = request()->get('paid') ? 1 : 0;
            
            // Delete old items
            $oldItems = Invoice::getItems($id);
            foreach ($oldItems as $item) {
                InvoiceItem::delete($item['id']);
            }
            
            // Calculate new total and create items
            $total = 0;
            for ($i = 0; $i < count($products); $i++) {
                if (!empty($products[$i]) && !empty($quantities[$i])) {
                    $price = $prices[$i] ?? 0;
                    $quantity = $quantities[$i];
                    $total += $price * $quantity;
                    
                    InvoiceItem::create([
                        'invoice_id' => $id,
                        'product_id' => $products[$i],
                        'quantity' => $quantity,
                        'price' => $price
                    ]);
                }
            }
            
            // Update invoice
            Invoice::update($id, [
                'user_id' => request()->get('user_id'),
                'total_amount' => $total,
                'paid' => $paid
            ]);
            
            $this->session->setFlash('success', 'Invoice updated successfully!');
            header("Location: /invoices/{$id}");
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to update invoice: ' . $e->getMessage()]]);
            return back();
        }
    }
}