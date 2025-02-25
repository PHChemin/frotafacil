<?php

namespace App\Controllers;

use App\Models\Fleet;
use App\Models\Route;
use App\Models\Truck;
use App\Models\TruckBrand;
use App\Models\TruckRoute;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class RoutesController extends Controller
{
    public function routes(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        $truck = $fleet->trucks()->findById($params['truck_id']);

        $allRoutes = Route::all();

        $this->render('manager/routes/index', compact('truck', 'allRoutes'));
    }

    public function storeUpdate(Request $request): void
    {
        $params = $request->getParams();

        /** @var Fleet $fleet */
        $fleet = $this->current_user->manager()->fleets()->findById($params['fleet_id']);

        /** @var Truck $truck */
        $truck = $fleet->trucks()->findById($params['truck_id']);

        if (!isset($params['routes'])) {
            $params['routes'] = [];
        }

        foreach ($truck->trucksRoutes()->get() as $truckRoute) {
            if (!$truckRoute->destroy()) {
                FlashMessage::danger('Erro ao remover rotas! Por favor tente novamente!');
                $this->redirectTo('/manager/fleets/' . $fleet->id . '/trucks/' . $truck->id);
                return;
            }
        }

        if (empty($params['routes'])) {
            FlashMessage::success('Rotas removidas com sucesso!');
            $this->redirectTo('/manager/fleets/' . $fleet->id . '/trucks/' . $truck->id);
            return;
        }

        foreach ($params['routes'] as $route_id) {
            $truckRoute = new TruckRoute([
                'route_id' => $route_id,
                'truck_id' => $truck->id
            ]);

            if (!$truckRoute->save()) {
                FlashMessage::danger('Erro ao salvar a relação do caminhão com a rota! Por favor tente novamente!');
                $allRoutes = Route::all();
                $this->render('manager/trucks/edit', compact('truck', 'allRoutes'));
                return;
            }
        }

        FlashMessage::success('Rotas atualizadas com sucesso!');
        $this->redirectTo('/manager/fleets/' . $fleet->id . '/trucks/' . $truck->id);
    }
}
