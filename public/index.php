<?php

use Elmasry\Http\Route;
use Elmasry\Http\Request;
use Elmasry\Http\Response;

require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../routes/web.php';

$request = new Request();
$response = new Response();
$router = new Route($request, $response);

echo $router->resolve();
        