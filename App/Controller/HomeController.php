<?php

namespace App\Controller;

use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;

class HomeController
{
    public function index()
    {
        return view('home');
    }

    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['is_authenticated'])) {
            header('Location: /login');
            exit;
        }

        // Get counts efficiently
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalInvoices = Invoice::count();

        // Get statistics via Models
        $invoiceStats = Invoice::getStats();
        $recentInvoices = Invoice::getRecent(5);
        $topProducts = Product::getTopSelling(5);

        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalInvoices' => $totalInvoices,
            'invoiceStats' => $invoiceStats,
            'recentInvoices' => $recentInvoices,
            'topProducts' => $topProducts
        ]);
    }
}