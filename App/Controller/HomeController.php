<?php

namespace App\Controller;

use Elmasry\View\View;

class HomeController
{
    public function index()
    {
      return View::make('home');
    }
}