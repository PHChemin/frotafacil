<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class ManagerController extends Controller
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
        $title = 'Página Inicial Gestores';
        $this->render('manager/index', compact('title'), 'application');
    }
}
