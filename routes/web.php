<?php

use Elmasry\Http\Route;
use App\Controller\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']); 

