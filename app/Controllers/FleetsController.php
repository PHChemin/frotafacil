<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class FleetsController extends Controller
{
    public function index(Request $request): void
    {
        $fleets = $this->current_user->manager()->fleets()->get();
        $this->render('manager/fleets/index', compact('fleets'), 'application');
    }

    public function new(): void
    {
        $fleet = $this->current_user->manager()->fleets()->new();

        $this->render('manager/fleets/new', compact('fleet'), 'application');
    }

    public function create(Request $request): void
    {
        $params = $request->getParams();
        $fleet = $this->current_user->manager()->fleets()->new($params['fleet']);

        if ($fleet->save()) {
            FlashMessage::success('Frota registrada com sucesso!');
            $this->redirectTo(route('fleets.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor verifique!');
            $this->render('manager/fleets/new', compact('fleet'), 'application');
        }
    }
}
