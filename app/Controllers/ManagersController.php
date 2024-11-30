<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class ManagersController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Página Inicial Gestores';
        $this->render('manager/index', compact('title'), 'application');
    }
}
