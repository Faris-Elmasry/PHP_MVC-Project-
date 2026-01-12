<?php
// app/Controllers/InvoiceController.php

namespace App\Controller;

use App\Models\Invoice;
use App\Models\Product;
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
        
        return view('invoices.create', ['products' => $products]);
    }
    
    /**
     * Store new invoice
     */
    public function store()
    {
        // Validate
        $v = new Validator();
        $v->setRules([
            'user_id' => 'required|numeric',
            'items' => 'required'
        ]);
        
        $v->make(request()->all());
        
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            return back();
        }
        
        // Parse items (expecting JSON or array)
        $items = json_decode(request()->get('items'), true);
        
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
}