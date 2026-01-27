<?php
// app/Models/Product.php

namespace App\Models;

use Elmasry\Database\Database;

class Product extends Model
{
    protected static $table = 'products';
    protected static $fillable = ['name', 'price', 'vat'];

    /**
     * Get products with pagination
     */
    public static function paginate($perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;

        $products = Database::select(
            "SELECT * FROM products LIMIT ? OFFSET ?",
            [(int) $perPage, (int) $offset]
        );

        $total = self::count();

        return [
            'data' => $products,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Search products by name
     */
    public static function search($query)
    {
        return Database::select(
            "SELECT * FROM products WHERE name LIKE ?",
            ["%{$query}%"]
        );
    }

    /**
     * Get price with VAT
     */
    public static function getPriceWithVat($id)
    {
        $product = self::find($id);
        if (!$product)
            return null;

        $price = $product['price'];
        $vat = $product['vat'];

        return $price + ($price * $vat / 100);
    }

    /**
     * Get top selling products
     */
    public static function getTopSelling($limit = 5)
    {
        return Database::select(
            "SELECT products.name, products.price, SUM(invoice_items.quantity) as total_sold
            FROM invoice_items
            JOIN products ON invoice_items.product_id = products.id
            GROUP BY products.id, products.name, products.price
            ORDER BY total_sold DESC
            LIMIT ?",
            [(int) $limit]
        );
    }
}