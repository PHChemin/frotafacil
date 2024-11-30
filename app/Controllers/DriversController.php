<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class DriversController extends Controller
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
        $title = 'Página Inicial Motoristas';
        $this->render('driver/index', compact('title'), 'application');
    }
}
