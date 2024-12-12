<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class FleetsController extends Controller
{
    public function index(Request $request): void
    {
        $fleets = $this->current_user->manager()->fleets()->get();
        $title = 'Frotas';
        $this->render('manager/fleets/index', compact('title', 'fleets'), 'application');
    }
}
