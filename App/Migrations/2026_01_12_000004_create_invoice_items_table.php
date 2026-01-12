<?php

use Elmasry\Database\Migration;
use Elmasry\Database\Schema;
use Elmasry\Database\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('CASCADE');
            $table->foreignId('product_id')->constrained('products')->onDelete('CASCADE');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
