<?php
// app/Models/Invoice.php

namespace App\Models;

use Elmasry\Database\Database;

class Invoice extends Model
{
    protected static $table = 'invoices';
    protected static $fillable = ['user_id', 'total_amount'];
    
  
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
}