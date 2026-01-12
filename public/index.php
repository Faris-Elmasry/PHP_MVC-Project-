<?php

use Elmasry\Http\Route;
use Elmasry\Http\Request;
use Elmasry\Http\Response;
use Elmasry\Support\Arr;
use Elmasry\Support\Hash;

require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../src/Support/helper.php';
require_once __DIR__ . '/../routes/web.php';

$request = new Request();
$response = new Response();
$router = new Route($request, $response);

 
 
app()->run();
 
 
// app()->session->setFlash('errors' , ['username' => ['empty']]);

 