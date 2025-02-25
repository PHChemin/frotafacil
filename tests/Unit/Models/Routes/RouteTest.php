<?php

namespace Tests\Unit\Models\Routes;

use App\Models\Driver;
use App\Models\Fleet;
use App\Models\Manager;
use App\Models\Route;
use App\Models\TruckBrand;
use App\Models\TruckRoute;
use App\Models\User;
use Tests\TestCase;

class RouteTest extends TestCase
{
    private Route $route;

    public function setUp(): void
    {
        parent::setUp();

        // Criando uma rota
        $this->route = new Route([
            'start_address' => 'Guarapuava',
            'arrival_address' => 'São Paulo',
            'distance' => 570,
            'value' => 5000
        ]);
        $this->route->save();
    }

    /**
     * ================= START OF TESTING ====================================
     */

    public function test_should_create_a_new_route(): void
    {
        $route = new Route([
            'start_address' => 'Curitiba',
            'arrival_address' => 'São Bento',
            'distance' => 270,
            'value' => 3000
        ]);
        $route->save();

        $this->assertTrue($route->save());
        $this->assertCount(2, Route::all());
    }

    public function test_should_return_all_routes(): void
    {
        $routes[] = $this->route;
        $routes[] = new Route([
            'start_address' => 'Curitiba',
            'arrival_address' => 'São Bento',
            'distance' => 270,
            'value' => 3000
        ]);
        $routes[1]->save();

        $all = Route::all();
        $this->assertCount(2, $all);
        $this->assertEquals($routes, $all);
    }

    public function test_destroy_should_remove_a_route(): void
    {
        $route = new Route([
            'start_address' => 'Curitiba',
            'arrival_address' => 'São Bento',
            'distance' => 270,
            'value' => 3000
        ]);
        $route->save();

        $this->assertCount(2, Route::all());
        $this->assertTrue($route->destroy());
        $this->assertCount(1, Route::all());
    }

    public function test_should_edit_route_attributes(): void
    {
        $this->route->start_address = 'Curitiba';
        $this->route->arrival_address = 'São Bento';
        $this->route->distance = 270;
        $this->route->value = 3000;

        $this->assertTrue($this->route->save());
        $this->assertEquals('Curitiba', $this->route->start_address);
        $this->assertEquals('São Bento', $this->route->arrival_address);
        $this->assertEquals(270, $this->route->distance);
        $this->assertEquals(3000, $this->route->value);
    }


    public function test_should_return_error_if_attributes_are_empty(): void
    {
        $route = new Route([
            'start_address' => '',
            'arrival_address' => '',
            'distance' => null,
            'value' => null
        ]);

        $this->assertFalse($route->isValid());
        $this->assertTrue($route->hasErrors());
        $this->assertEquals('start_address cannot be empty!', $route->errors('start_address'));
        $this->assertEquals('arrival_address cannot be empty!', $route->errors('arrival_address'));
        $this->assertEquals('distance cannot be empty!', $route->errors('distance'));
        $this->assertEquals('value cannot be empty!', $route->errors('value'));
    }

    public function test_should_return_trucks_routes(): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);
        $user->save();

        $manager = new Manager([
            'user_id' => $user->id
        ]);
        $manager->save();

        $user2 = new User([
            'cpf' => '98765432100',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password',
        ]);
        $user2->save();

        $driver = new Driver([
            'user_id' => $user2->id,
        ]);
        $driver->save();

        $fleet = new Fleet([
            'name' => 'Fleet of manager 1',
            'manager_id' => $user->manager()->id
        ]);
        $fleet->save();

        $truckBrand = new TruckBrand([
            'name' => 'Scania'
        ]);
        $truckBrand->save();

        $truck = $fleet->trucks()->new([
            'truck_brand_id' => $truckBrand->id,
            'model' => 'Model Y',
            'color' => 'Blue',
            'plate' => 'DEF5678',
            'driver_id' => $driver->id,
        ]);
        $truck->save();

        $truckRoute = new TruckRoute([
            'truck_id' => $truck->id,
            'route_id' => $this->route->id
        ]);
        $truckRoute->save();

        $this->assertCount(1, $this->route->trucksRoutes()->get());
    }
}
