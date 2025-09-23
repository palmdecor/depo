<?php

namespace App\Controllers;

use App\Support\View;

class HomeController
{
    public function index(): string
    {
        return View::render('home', [
            'title' => 'Depo Platformu',
        ]);
    }
}
