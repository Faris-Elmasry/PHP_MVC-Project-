<?php

namespace App\Controller;

use Elmasry\Database\Database;
use Elmasry\Support\Hash;
use Elmasry\Support\Session;
use Elmasry\Validation\Validator;
use Elmasry\View\View;

class LoginController
{
    protected $session;

    public function __construct()
    {
        // تأكد إن الـ session شغالة
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = new Session();
    }

    public function index()
    {
        if ($this->session->has('is_authenticated')) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login()
    {
        // 1 - Validate input
        $v = new Validator();
        $v->setRules([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $v->make(request()->all());

        // 2 - If validation fails, redirect back with errors
        if (!$v->passes()) {
            $this->session->setFlash('errors', $v->errors());
            $this->session->setFlash('old', request()->all());

            // Debug: تأكد إن الـ errors اتخزنت
            error_log("Validation failed. Errors: " . print_r($v->errors(), true));

            return redirect('/login');
        }

        // 3 - Get credentials
        $email = request()->get('email');
        $password = request()->get('password');

        // 4 - Find user by email
        try {
            $db = Database::connection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Debug
            error_log("User found: " . ($user ? 'Yes' : 'No'));
            if ($user) {
                error_log("Password verify: " . (Hash::verify($password, $user['password']) ? 'Yes' : 'No'));
            }

            // 5 - Check if user exists and password is correct
            if (!$user || !Hash::verify($password, $user['password'])) {
                $this->session->setFlash('errors', [
                    'email' => ['Invalid email or password.']
                ]);
                $this->session->setFlash('old', [
                    'email' => $email
                ]);

                error_log("Login failed: Invalid credentials");

                return redirect('/login');
            }

            // 6 - Login successful - Set session
            $this->session->set('user_id', $user['id']);
            $this->session->set('user_name', $user['name']);
            $this->session->set('user_email', $user['email']);
            $this->session->set('is_authenticated', true);

            // 7 - Handle "Remember Me" (optional)
            if (request()->get('remember')) {
                $this->session->set('remember_me', true);
            }

            // 8 - Redirect to home
            $this->session->setFlash('success', 'Welcome back, ' . $user['name'] . '!');

            return redirect('/dashboard');

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());

            $this->session->setFlash('errors', [
                'email' => ['Login failed. Please try again.']
            ]);
            $this->session->setFlash('old', [
                'email' => $email
            ]);

            return redirect('/login');
        }
    }

    public function logout()
    {
        // Clear all authentication session data
        $this->session->remove('user_id');
        $this->session->remove('user_name');
        $this->session->remove('user_email');
        $this->session->remove('is_authenticated');
        $this->session->remove('remember_me');

        $this->session->setFlash('success', 'You have been logged out successfully.');

        return redirect('/');
    }
}

// Helper function for redirect
function redirect($url)
{
    header("Location: $url");
    exit;
}