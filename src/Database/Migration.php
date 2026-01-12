<?php

namespace Elmasry\Database;

abstract class Migration
{
    /**
     * Run the migrations (create tables, add columns, etc.)
     */
    abstract public function up(): void;

    /**
     * Reverse the migrations (drop tables, remove columns, etc.)
     */
    abstract public function down(): void;
}
