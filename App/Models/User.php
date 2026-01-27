<?php

namespace App\Models;

use Elmasry\Database\Database;
use Elmasry\Support\Hash;

class User extends Model
{
    protected static $table = 'users';
    protected static $fillable = ['name', 'email', 'password', 'phone', 'address'];

    public static function paginate($limit = 10, $page = 1, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $params = [];

        $query = "SELECT * FROM " . static::$table;
        $countQuery = "SELECT COUNT(*) as total FROM " . static::$table;

        if ($search) {
            $whereClause = " WHERE name LIKE ? OR email LIKE ?";
            $query .= $whereClause;
            $countQuery .= $whereClause;
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

        $users = Database::select($query, $params);
        $totalResult = Database::select($countQuery, array_slice($params, 0, 2)); // Use same params for count
        $total = $totalResult[0]['total'] ?? 0;
        $totalPages = ceil($total / $limit);

        return [
            'data' => $users,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit
        ];
    }
}