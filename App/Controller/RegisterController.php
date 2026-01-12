<?php
namespace App\Controller;

use Elmasry\Database\Database;
use Elmasry\Support\Hash;
use Elmasry\Support\Session;
use Elmasry\Validation\Validator;
use Elmasry\View\View;

class RegisterController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function index()
    {
        return View('auth.register');
    }

    public function store()
    {
        // 1 - Validate input
        $v = new Validator();
        $v->setRules([
            'name'     => 'required|between:3,100',
            'email'    => 'required|email|unique:users,email',
            'address'  => '',
            'phone'    => '',
            'password' => 'required|between:6,64|confirmed',
            'password_confirmation' => 'required'
        ]);
        
        $v->setAliases([
            'password_confirmation' => 'password confirmation'
        ]);
        
        $v->make(request()->all());
        
        // 2 - If validation fails, redirect back with errors
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            $this->session->setFlash('old', request()->all());
            return back();
        }
        
        // 3 - Insert user into database
        try {
            $data = request()->all();
            
            Database::execute(
                "INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)",
                [
                    $data['name'],
                    $data['email'],
                    Hash::make($data['password']),
                    $data['phone'] ?? null,
                    $data['address'] ?? null
                ]
            );
            
            $this->session->setFlash('success', 'Registration successful! You can now login.');
            header('Location: /login');
            exit;
            
        } catch (\PDOException $e) {
            $this->session->setFlash('errors', ['email' => ['Registration failed. Please try again.']]);
            $this->session->setFlash('old', request()->all());
            return back();
        }
    }
}