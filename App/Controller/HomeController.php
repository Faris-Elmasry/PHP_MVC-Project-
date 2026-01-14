<?php

namespace App\Controller;

use Elmasry\View\View;
use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;
use Elmasry\Database\Database;

class HomeController
{
    public function index()
    {
      return View::make('home');
    }

    public function dashboard()
    {
        // Get counts
        $totalUsers = count(User::all());
        $totalProducts = count(Product::all());
        $totalInvoices = count(Invoice::all());
        
        // Get invoice statistics
        $invoiceStats = Database::select(
            "SELECT 
                COUNT(*) as total,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN paid = 1 THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN paid = 0 OR paid IS NULL THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN paid = 1 THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN paid = 0 OR paid IS NULL THEN total_amount ELSE 0 END) as unpaid_amount
            FROM invoices"
        );
        
        // Get recent invoices
        $recentInvoices = Database::select(
            "SELECT invoices.*, users.name as user_name, users.email as user_email 
            FROM invoices 
            JOIN users ON invoices.user_id = users.id 
            ORDER BY invoices.created_at DESC 
            LIMIT 5"
        );
        
        // Get top products (most sold)
        $topProducts = Database::select(
            "SELECT products.name, products.price, SUM(invoice_items.quantity) as total_sold
            FROM invoice_items
            JOIN products ON invoice_items.product_id = products.id
            GROUP BY products.id, products.name, products.price
            ORDER BY total_sold DESC
            LIMIT 5"
        );

        return View::make('dashboard', [
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalInvoices' => $totalInvoices,
            'invoiceStats' => $invoiceStats[0] ?? [],
            'recentInvoices' => $recentInvoices,
            'topProducts' => $topProducts
        ]);
    }
}