<?php

namespace App\Controllers;

use App\Models\Fleet;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class FleetsController extends Controller
{
    public function index(Request $request): void
    {
        $fleets = $this->current_user->manager()->fleets()->get();
        $this->render('manager/fleets/index', compact('fleets'));
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $trucks = $fleet->trucks()->get();

        $this->render('manager/fleets/show', compact('fleet', 'trucks'));
    }

    public function new(): void
    {
        $fleet = $this->current_user->manager()->fleets()->new();

        $this->render('manager/fleets/new', compact('fleet'));
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
            $this->render('manager/fleets/new', compact('fleet'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $this->render('manager/fleets/edit', compact('fleet'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('fleet_id');
        $params = $request->getParam('fleet');

        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($id);
        $fleet->name = $params['name'];

        if ($fleet->save()) {
            FlashMessage::success('Frota editada com sucesso!');
            $this->redirectTo('/manager/fleets/' . $fleet->id);
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor verifique!');
            $this->render('manager/fleets/edit', compact('fleet'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);
        $fleet->destroy();

        FlashMessage::success('Frota removida com sucesso!');
        $this->redirectTo(route('fleets.index'));
    }
}
