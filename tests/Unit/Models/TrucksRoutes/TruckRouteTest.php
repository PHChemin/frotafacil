<?php

namespace Tests\Unit\Models\TrucksRoutes;

use App\Models\Truck;
use App\Models\Fleet;
use App\Models\Driver;
use App\Models\TruckBrand;
use App\Models\User;
use App\Models\Manager;
use App\Models\Route;
use App\Models\TruckRoute;
use Tests\TestCase;

class TruckRouteTest extends TestCase
{
    private User $user;
    private User $user2;
    private Fleet $fleet;
    private Driver $driver;
    private TruckBrand $truckBrand;
    private Truck $truck;
    private Route $route;
    private Route $route2;
    private TruckRoute $truckRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->fleetAndManagerSetUp();
        $this->driverSetUp();
        $this->truckSetUp();
        $this->routeSetUp();

        // Criando um TruckRoute
        $this->truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route->id
        ]);
        $this->truckRoute->save();
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
     * =================== START OF TESTING ===================
     */

    public function test_should_create_a_new_truck_route(): void
    {
        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route->id
        ]);

        $this->assertTrue($truckRoute->save());
        $this->assertCount(2, TruckRoute::all());
    }

    public function test_should_return_all_truck_route(): void
    {
        $trucksRoutes[] = $this->truckRoute;
        $trucksRoutes[] = $this->truck->trucksRoutes()->new([
            'route_id' => $this->route2->id,
        ]);
        $trucksRoutes[1]->save();

        $all = TruckRoute::all();
        $this->assertCount(2, $all);
        $this->assertEquals($trucksRoutes, $all);
    }

    public function test_destroy_should_remove_a_truck_route(): void
    {
        $truckRoute = $this->truck->trucksRoutes()->new([
            'route_id' => $this->route2->id,
        ]);
        $truckRoute->save();

        $this->assertCount(2, TruckRoute::all());
        $this->assertTrue($truckRoute->destroy());
        $this->assertCount(1, TruckRoute::all());
    }

    public function test_should_return_error_if_truck_id_is_empty(): void
    {
        $truckRoute = new TruckRoute([
            'truck_id' => null,
            'route_id' => $this->route->id
        ]);

        $this->assertFalse($truckRoute->isValid());
        $this->assertTrue($truckRoute->hasErrors());
        $this->assertEquals('truck_id cannot be empty!', $truckRoute->errors('truck_id'));
    }

    public function test_should_return_error_if_route_id_is_empty(): void
    {
        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => null,
        ]);

        $this->assertFalse($truckRoute->isValid());
        $this->assertTrue($truckRoute->hasErrors());
        $this->assertEquals('route_id cannot be empty!', $truckRoute->errors('route_id'));
    }

    public function test_should_return_error_if_truck_id_doesnot_exist(): void
    {
        $truckRoute = new TruckRoute([
            'truck_id' => 999,
            'route_id' => $this->route->id
        ]);

        $this->assertFalse($truckRoute->isValid());
        $this->assertTrue($truckRoute->hasErrors());
        $this->assertEquals('truck_id does not exist!', $truckRoute->errors('truck_id'));
    }

    public function test_should_return_error_if_route_id_doesnot_exist(): void
    {
        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => 999
        ]);

        $this->assertFalse($truckRoute->isValid());
        $this->assertTrue($truckRoute->hasErrors());
        $this->assertEquals('route_id does not exist!', $truckRoute->errors('route_id'));
    }

    public function test_should_remove_trucks_routes_if_removed_their_truck(): void
    {
        $this->assertCount(1, TruckRoute::all());
        $this->assertTrue($this->truck->destroy());
        $this->assertCount(0, Truck::all());

        $this->assertCount(0, TruckRoute::all());
    }
}
