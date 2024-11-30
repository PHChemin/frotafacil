<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class HomeController extends Controller
{
    protected ?\App\Models\User $current_user;

    public function __construct()
    {
        $this->current_user = $this->getCurrentUser();
    }

    public function getCurrentUser(): ?\App\Models\User
    {
        return Auth::user();
    }

    public function index(Request $request): void
    {
        $title = 'Inicio';
        $this->render('home/index', compact('title'), 'application');
    }

    public function managerUser(Request $request): void
    {
        $title = 'Gestor';
        $this->render('home/index', compact('title'), 'application');
    }

    public function driverUser(Request $request): void
    {
        $title = 'Motorista';
        $this->render('home/index', compact('title'), 'application');
    }
}
