<?php
// app/Models/Model.php

namespace App\Models;

use Elmasry\Database\Database;

abstract class Model
{
    protected static $table;
    protected static $fillable = [];
    
    /**
     * Get all records
     */
    public static function all()
    {
        $table = static::$table;
        return Database::select("SELECT * FROM {$table}");
    }
    
    /**
     * Find a record by ID
     */
    public static function find($id)
    {
        $table = static::$table;
        $result = Database::select("SELECT * FROM {$table} WHERE id = ? LIMIT 1", [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Create a new record
     */
    public static function create(array $data)
    {
        $table = static::$table;
        $fillable = static::$fillable;
        
        // Filter only fillable fields
        $filtered = array_intersect_key($data, array_flip($fillable));
        
        $columns = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        Database::execute($sql, array_values($filtered));
        
        return Database::lastInsertId();
    }
    
    /**
     * Update a record
     */
    public static function update($id, array $data)
    {
        $table = static::$table;
        $fillable = static::$fillable;
        
        // Filter only fillable fields
        $filtered = array_intersect_key($data, array_flip($fillable));
        
        $sets = [];
        foreach (array_keys($filtered) as $column) {
            $sets[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE id = ?";
        $values = array_values($filtered);
        $values[] = $id;
        
        return Database::execute($sql, $values);
    }
    
    /**
     * Delete a record
     */
    public static function delete($id)
    {
        $table = static::$table;
        return Database::execute("DELETE FROM {$table} WHERE id = ?", [$id]);
    }
    
    /**
     * Custom query
     */
    public static function where($column, $operator, $value = null)
    {
        $table = static::$table;
        
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        return Database::select(
            "SELECT * FROM {$table} WHERE {$column} {$operator} ?",
            [$value]
        );
    }
    
    /**
     * Count records
     */
    public static function count()
    {
        $table = static::$table;
        $result = Database::select("SELECT COUNT(*) as count FROM {$table}");
        return $result[0]['count'] ?? 0;
    }
}