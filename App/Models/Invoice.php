<?php
// app/Models/Invoice.php

namespace App\Models;

use Elmasry\Database\Database;

class Invoice extends Model
{
    protected static $table = 'invoices';
    protected static $fillable = ['user_id', 'total_amount', 'paid'];


    public static function findWithUser($id)
    {
        $result = Database::select(
            "SELECT 
                invoices.*,
                users.name as user_name,
                users.email as user_email,
                users.phone as user_phone,
                users.address as user_address
            FROM invoices
            JOIN users ON invoices.user_id = users.id
            WHERE invoices.id = ?
            LIMIT 1",
            [$id]
        );

        return $result[0] ?? null;
    }


    public static function getItems($invoiceId)
    {
        return Database::select(
            "SELECT 
                invoice_items.*,
                products.name as product_name,
                products.price as product_price,
                products.vat as product_vat
            FROM invoice_items
            JOIN products ON invoice_items.product_id = products.id
            WHERE invoice_items.invoice_id = ?",
            [$invoiceId]
        );
    }

    public static function createWithItems($userId, array $items)
    {
        Database::beginTransaction();

        try {
            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $priceWithVat = $product['price'] * (1 + $product['vat'] / 100);
                $total += $priceWithVat * $item['quantity'];
            }

            // Create invoice
            $invoiceId = self::create([
                'user_id' => $userId,
                'total_amount' => $total
            ]);

            // Create invoice items
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $priceWithVat = $product['price'] * (1 + $product['vat'] / 100);

                InvoiceItem::create([
                    'invoice_id' => $invoiceId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $priceWithVat
                ]);
            }

            Database::commit();
            return $invoiceId;

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    public static function updateWithItems($id, $data, array $products, array $quantities, array $prices)
    {
        Database::beginTransaction();

        try {
            // Delete old items
            $oldItems = self::getItems($id);
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
            self::update($id, [
                'user_id' => $data['user_id'],
                'total_amount' => $total,
                'paid' => $data['paid']
            ]);

            Database::commit();

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }


    public static function allWithUsers()
    {
        return Database::select(
            "SELECT 
                invoices.*,
                users.name as user_name,
                users.email as user_email
            FROM invoices
            JOIN users ON invoices.user_id = users.id
            ORDER BY invoices.created_at DESC"
        );
    }

    public static function getStats()
    {
        $result = Database::select(
            "SELECT 
                COUNT(*) as total,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN paid = 1 THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN paid = 0 OR paid IS NULL THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN paid = 1 THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN paid = 0 OR paid IS NULL THEN total_amount ELSE 0 END) as unpaid_amount
            FROM invoices"
        );
        return $result[0] ?? [];
    }

    public static function getRecent($limit = 5)
    {
        return Database::select(
            "SELECT invoices.*, users.name as user_name, users.email as user_email 
            FROM invoices 
            JOIN users ON invoices.user_id = users.id 
            ORDER BY invoices.created_at DESC 
            LIMIT ?",
            [$limit]
        );
    }
}