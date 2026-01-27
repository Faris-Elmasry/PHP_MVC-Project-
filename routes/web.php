<?php

use Elmasry\Http\Route;
use App\Controller\HomeController;
use App\Controller\RegisterController;
use App\Controller\LoginController;
use App\Controller\ProductController;
use App\Controller\InvoiceController;
use App\Controller\UserController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/dashboard', [HomeController::class, 'dashboard']);

//registration routes
Route::get('/signup', [RegisterController::class, 'index']);
Route::post('/signup', [RegisterController::class, 'store']);

//login routes
Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

//products routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
Route::post('/products/{id}', [ProductController::class, 'update']);
Route::post('/products/{id}/delete', [ProductController::class, 'destroy']);

//invoices routes
Route::get('/invoices', [InvoiceController::class, 'index']);
Route::get('/invoices/create', [InvoiceController::class, 'create']);
Route::post('/invoices', [InvoiceController::class, 'store']);
Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit']);
Route::post('/invoices/{id}', [InvoiceController::class, 'update']);
Route::post('/invoices/{id}/delete', [InvoiceController::class, 'destroy']);
//clients routes
Route::get('/clients', [UserController::class, 'index']);
Route::get('/clients/create', [UserController::class, 'create']);
Route::post('/clients', [UserController::class, 'store']);
Route::get('/clients/{id}/edit', [UserController::class, 'edit']);
Route::post('/clients/{id}', [UserController::class, 'update']);
Route::post('/clients/{id}/delete', [UserController::class, 'destroy']);
