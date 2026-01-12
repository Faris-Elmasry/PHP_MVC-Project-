<?php
// app/Models/InvoiceItem.php

namespace App\Models;

class InvoiceItem extends Model
{
    protected static $table = 'invoice_items';
    protected static $fillable = ['invoice_id', 'product_id', 'quantity', 'price'];
}