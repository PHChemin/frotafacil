<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class DriversController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Página Inicial Motoristas';
        $this->render('driver/index', compact('title'));
    }
}
