<?php

namespace App\Controllers;

use App\Models\Fleet;
use App\Models\TruckBrand;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class TrucksController extends Controller
{
    public function show(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->findById($params['truck_id']);

        $this->render('manager/trucks/show', compact('truck'));
    }

    public function new(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->new();
        $truckBrands = TruckBrand::all();

        $this->render('manager/trucks/new', compact('truck', 'truckBrands'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->new($params['truck']);

        if ($truck->save()) {
            FlashMessage::success('Caminhão registrado com sucesso!');
            $this->redirectTo('/manager/fleets/' . $fleet->id);
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor verifique!');

            $truckBrands = TruckBrand::all();

            $this->render('manager/trucks/new', compact('truck', 'truckBrands'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->findById($params['truck_id']);

        $this->render('manager/trucks/edit', compact('truck'));
    }

    public function update(Request $request): void
    {
        $params = $request->getParams();

        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->findById($params['truck_id']);

        $truck->model = $params['truck']['model'];
        $truck->color = $params['truck']['color'];
        $truck->plate = $params['truck']['plate'];

        if ($truck->save()) {
            FlashMessage::success('Caminhão editado com sucesso!');
            $this->redirectTo('/manager/fleets/' . $fleet->id);
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor verifique!');
            $this->render('manager/trucks/edit', compact('truck'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->findById($params['truck_id']);
        $truck->destroy();

        FlashMessage::success('Caminhão deletado com sucesso!');
        $this->redirectTo('/manager/fleets/' . $fleet->id);
    }
}
