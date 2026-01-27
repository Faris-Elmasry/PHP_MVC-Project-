<?php
namespace App\Controller;

use App\Models\User;
use Elmasry\Support\Hash;
use Elmasry\Support\Session;
use Elmasry\Validation\Validator;

class RegisterController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function index()
    {
        if ($this->session->has('is_authenticated')) {
            header('Location: /dashboard');
            exit;
        }
        return view('auth.register');
    }

    public function store()
    {
        // 1 - Validate input
        $v = new Validator();
        $v->setRules([
            'name' => 'required|between:3,100',
            'email' => 'required|email|unique:users,email',
            'address' => '',
            'phone' => '',
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
            User::create([
                'name' => request()->get('name'),
                'email' => request()->get('email'),
                'password' => Hash::make(request()->get('password')),
                'phone' => request()->get('phone'),
                'address' => request()->get('address')
            ]);

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