<?php

namespace Tests\Unit\Controllers;

use App\Models\Driver;
use App\Models\Fleet;
use App\Models\User;
use App\Models\Manager;
use App\Models\Truck;
use App\Models\TruckBrand;

class TrucksControllerTest extends ControllerTestCase
{
    private User $user;
    private User $user2;
    private Manager $manager;
    private Driver $driver;
    private Fleet $fleet;
    private Truck $truck;
    private TruckBrand $truckBrand;

    public function setUp(): void
    {
        parent::setUp();

        $this->truckBrand = new TruckBrand([
            'name' => 'Scania'
        ]);
        $this->truckBrand->save();

        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $this->user->save();

        $this->user2 = new User([
            'cpf' => '12345678902',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $this->user2->save();

        $this->manager = new Manager(['user_id' => $this->user->id]);
        $this->manager->save();

        $this->driver = new Driver(['user_id' => $this->user2->id]);
        $this->driver->save();

        $this->fleet = new Fleet(['name' => 'Fleet of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet->save();

        $this->truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => '113h',
            'color' => 'branco',
            'plate' => 'ABC4B77',
            'fleet_id' => $this->fleet->id,
            'driver_id' => $this->driver->id
        ]);
        $this->truck->save();

        $_SESSION['user']['id'] = $this->user->id;
    }

    public function test_list_all_trucks(): void
    {
        $trucks = $this->fleet->trucks()->get();

        $response = $this->get(
            action: 'show',
            controllerName: 'App\Controllers\FleetsController',
            params: ['fleet_id' => $this->fleet->id]
        );

        $this->assertStringContainsString($this->fleet->name, $response);

        foreach ($trucks as $truck) {
            /** @var \App\Models\Truck $truck */
            $this->assertMatchesRegularExpression("/{$truck->truckBrand()->get()->name} {$truck->model}/", $response);
        }
    }

    public function test_new_truck(): void
    {
        $response = $this->get(
            action: 'new',
            controllerName: 'App\Controllers\TrucksController',
            params: ['fleet_id' => $this->fleet->id]
        );

        $this->assertStringContainsString('Novo caminhão', $response);
    }

    public function test_successfully_create_truck(): void
    {
        $params = [
            'fleet_id' => $this->fleet->id,
            'truck'  => [
                'truck_brand_id' => $this->truckBrand->id,
                'model' => '113h',
                'color' => 'branco',
                'plate' => 'ABC4B67',
                'fleet_id' => $this->fleet->id,
                'driver_id' => $this->driver->id
            ]];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\TrucksController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/Location: \/manager\/fleets\/{$this->fleet->id}/", $response);
    }

    public function test_unsuccessfully_create_truck(): void
    {
        $params = [
            'fleet_id' => $this->fleet->id,
            'truck'  => [
                'truck_brand_id' => $this->truckBrand->id,
                'model' => '',
                'color' => '',
                'plate' => '',
                'fleet_id' => $this->fleet->id,
                'driver_id' => $this->driver->id
            ]];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\TrucksController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/não pode estar vazio!/", $response);
    }

    public function test_edit_truck(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();

        $truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => '113h',
            'color' => 'branco',
            'plate' => 'ABC4B67',
            'fleet_id' => $fleet->id,
            'driver_id' => $this->driver->id
        ]);
        $truck->save();

        $response = $this->get(
            action: 'edit',
            controllerName: 'App\Controllers\TrucksController',
            params: ['fleet_id' => $fleet->id, 'truck_id' => $truck->id]
        );

        $this->assertMatchesRegularExpression("/Editar {$truck->truckBrand()->get()->name} {$truck->model}/", $response);

        $regex = '/<input\s+[^>]*type=[\'"]text[\'"][^>]*name=[\'"]truck\[model\][\'"][^>]*>/i';
        $this->assertMatchesRegularExpression($regex, $response);
    }

    public function test_successfully_update_truck(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();

        $truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => '113h',
            'color' => 'branco',
            'plate' => 'ABC4B67',
            'fleet_id' => $fleet->id,
            'driver_id' => $this->driver->id
        ]);
        $truck->save();

        $params = [
            'fleet_id' => $fleet->id,
            'truck_id' => $truck->id,
            'truck' => [
                'model' => '124G',
                'color' => 'azul',
                'plate' => 'XYZ9K88'
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\TrucksController',
            params: $params
        );

        $this->assertMatchesRegularExpression(
            "/Location: \/manager\/fleets\/{$fleet->id}/",
            $response
        );
    }

    public function test_unsuccessfully_update_truck(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();

        $truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => '113h',
            'color' => 'branco',
            'plate' => 'ABC4B67',
            'fleet_id' => $fleet->id,
            'driver_id' => $this->driver->id
        ]);
        $truck->save();

        $params = [
            'fleet_id' => $fleet->id,
            'truck_id' => $truck->id,
            'truck' => [
                'model' => '',
                'color' => 'azul',
                'plate' => 'XYZ9K88'
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\TrucksController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/não pode estar vazio!/", $response);
    }
}
