<?php
// app/Controllers/ProductController.php

namespace App\Controller;

use App\Models\Product;
use Elmasry\Support\Session;
use Elmasry\Validation\Validator;

class ProductController
{
    protected $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    /**
     * Display all products
     */
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $products = Product::paginate(10, $page);
        
        return view('products.index', ['products' => $products]);
    }
    
    /**
     * Show create form
     */
    public function create()
    {
        return view('products.create');
    }
    
    /**
     * Store new product
     */
    public function store()
    {
        // Validate
        $v = new Validator();
        $v->setRules([
            'name' => 'required|between:3,255',
            'price' => 'required|numeric',
            'vat' => 'required|numeric'
        ]);
        
        $v->make(request()->all());
        
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            $this->session->setFlash('old', request()->all());
            return back();
        }
        
        // Create product
        try {
            Product::create([
                'name' => request()->get('name'),
                'price' => request()->get('price'),
                'vat' => request()->get('vat')
            ]);
            
            $this->session->setFlash('success', 'Product created successfully!');
            header('Location: /products');
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to create product.']]);
            return back();
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            $this->session->setFlash('errors', ['general' => ['Product not found.']]);
            header('Location: /products');
            exit;
        }
        
        return view('products.edit', ['product' => $product]);
    }
    
    /**
     * Update product
     */
    public function update($id)
    {
        // Validate
        $v = new Validator();
        $v->setRules([
            'name' => 'required|between:3,255',
            'price' => 'required|numeric',
            'vat' => 'required|numeric'
        ]);
        
        $v->make(request()->all());
        
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            $this->session->setFlash('old', request()->all());
            return back();
        }
        
        // Update product
        try {
            Product::update($id, [
                'name' => request()->get('name'),
                'price' => request()->get('price'),
                'vat' => request()->get('vat')
            ]);
            
            $this->session->setFlash('success', 'Product updated successfully!');
            header('Location: /products');
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to update product.']]);
            return back();
        }
    }
    
    /**
     * Delete product
     */
    public function destroy($id)
    {
        try {
            Product::delete($id);
            
            $this->session->setFlash('success', 'Product deleted successfully!');
            header('Location: /products');
            exit;
            
        } catch (\Exception $e) {
            $this->session->setFlash('errors', ['general' => ['Failed to delete product.']]);
            return back();
        }
    }
    
    /**
     * Search products
     */
    public function search()
    {
        $query = request()->get('q', '');
        $products = Product::search($query);
        
        return view('products.index', ['products' => ['data' => $products]]);
    }
}