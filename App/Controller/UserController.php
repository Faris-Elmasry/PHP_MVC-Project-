<?php

namespace App\Controller;

use App\Models\User;
use Elmasry\Support\Hash;
use Elmasry\Validation\Validator;

class UserController
{
    public function index()
    {
        $page = request()->get('page') ?? 1;
        $search = request()->get('search') ?? '';
        $limit = 10;

        $result = User::paginate($limit, $page, $search);

        return view('clients.index', [
            'users' => $result['data'],
            'total' => $result['total'],
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'search' => $search
        ]);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store()
    {
        $v = new Validator();
        $v->setRules([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $v->make(request()->all());

        if (!$v->passes()) {
            app()->session->setFlash('errors', $v->errors());
            app()->session->setFlash('old', request()->all());
            return back();
        }

        User::create([
            'name' => request()->get('name'),
            'email' => request()->get('email'),
            'password' => Hash::make(request()->get('password')),
            'phone' => request()->get('phone'),
            'address' => request()->get('address'),
        ]);

        app()->session->setFlash('success', 'Client created successfully');
        return redirect('/clients');
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('/clients');
        }
        return view('clients.edit', ['user' => $user]);
    }

    public function update($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('/clients');
        }

        $v = new Validator();
        $rules = [
            'name' => 'required|min:3',
            'email' => "required|email|unique:users,email,$id",
        ];

        if (request()->get('password')) {
            $rules['password'] = 'min:6';
        }

        $v->setRules($rules);
        $v->make(request()->all());

        if (!$v->passes()) {
            app()->session->setFlash('errors', $v->errors());
            app()->session->setFlash('old', request()->all());
            return back();
        }

        $data = [
            'name' => request()->get('name'),
            'email' => request()->get('email'),
            'phone' => request()->get('phone'),
            'address' => request()->get('address'),
        ];

        if (request()->get('password')) {
            $data['password'] = Hash::make(request()->get('password'));
        }

        User::update($id, $data);

        app()->session->setFlash('success', 'Client updated successfully');
        return redirect('/clients');
    }

    public function destroy($id)
    {
        User::delete($id);
        app()->session->setFlash('success', 'Client deleted successfully');
        return redirect('/clients');
    }
}
