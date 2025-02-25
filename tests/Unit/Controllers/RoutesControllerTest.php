<?php

namespace Tests\Unit\Controllers;

use App\Models\Driver;
use App\Models\Fleet;
use App\Models\User;
use App\Models\Manager;
use App\Models\Route;
use App\Models\Truck;
use App\Models\TruckBrand;

class RoutesControllerTest extends ControllerTestCase
{
    private User $user;
    private User $user2;
    private Fleet $fleet;
    private Driver $driver;
    private TruckBrand $truckBrand;
    private Truck $truck;
    private Route $route;
    private Route $route2;

    public function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->fleetAndManagerSetUp();
        $this->driverSetUp();
        $this->truckSetUp();
        $this->routeSetUp();

        $_SESSION['user']['id'] = $this->user->id;
    }

    /**
     * =================== SETUPS ===================
     */

    private function userSetUp(): void
    {
        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $this->user->save();

        $this->user2 = new User([
            'cpf' => '98765432100',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $this->user2->save();
    }

    private function fleetAndManagerSetUp(): void
    {
        $manager = new Manager(['user_id' => $this->user->id]);
        $manager->save();

        $this->fleet = new Fleet(['name' => 'Fleet of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet->save();
    }

    private function driverSetUp(): void
    {
        $this->driver = new Driver(['user_id' => $this->user2->id]);
        $this->driver->save();
    }

    private function truckSetUp(): void
    {
        $this->truckBrand = new TruckBrand(['name' => 'Scania']);
        $this->truckBrand->save();

        $this->truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model X',
            'color' => 'Red',
            'plate' => 'ABC1234',
            'fleet_id' => $this->fleet->id,
            'driver_id' => $this->driver->id,
        ]);
        $this->truck->save();
    }

    private function routeSetUp(): void
    {
        $this->route = new Route([
            'start_address' => 'Guarapuava',
            'arrival_address' => 'São Paulo',
            'distance' => 270,
            'value' => 3000
        ]);
        $this->route->save();

        $this->route2 = new Route([
            'start_address' => 'Pitanga',
            'arrival_address' => 'Paranaguá',
            'distance' => 130,
            'value' => 2000
        ]);
        $this->route2->save();
    }

    /**
     * ================ START OF TESTING ===================================================
     */

    public function test_all_routes(): void
    {
        $response = $this->get(
            action: 'routes',
            controllerName: 'App\Controllers\RoutesController',
            params: ['fleet_id' => $this->fleet->id, 'truck_id' => $this->truck->id]
        );

        $this->assertStringContainsString('Gerenciar Rotas do Caminhão', $response);

        $routes = Route::all();

        foreach ($routes as $route) {
            /** @var \App\Models\Route $route */
            $this->assertMatchesRegularExpression("/{$route->start_address}/", $response);
        }
    }

    public function test_successfully_create_trucks_routes(): void
    {
        $response = $this->post(
            action: 'storeUpdate',
            controllerName: 'App\Controllers\RoutesController',
            params: [
                'fleet_id' => $this->fleet->id,
                'truck_id' => $this->truck->id,
                'routes' => [$this->route->id, $this->route2->id]
            ]
        );

        $this->assertMatchesRegularExpression(
            "#Location: /manager/fleets/{$this->fleet->id}/trucks/{$this->truck->id}#",
            $response
        );
    }

    public function test_unsuccessfully_create_trucks_routes(): void
    {
        $response = $this->post(
            action: 'storeUpdate',
            controllerName: 'App\Controllers\RoutesController',
            params: [
                'fleet_id' => $this->fleet->id,
                'truck_id' => $this->truck->id,
                'routes' => [999, 998] // IDs que não existem
                ]
        );

        $this->assertMatchesRegularExpression(
            "#Erro ao salvar a relação do caminhão com a rota! Por favor tente novamente!#",
            $response
        );
    }
}
