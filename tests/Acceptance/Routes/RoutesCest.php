<?php

namespace Tests\Acceptance\Routes;

use App\Models\Driver;
use App\Models\Fleet;
use App\Models\Manager;
use App\Models\Route;
use App\Models\Truck;
use App\Models\TruckBrand;
use App\Models\TruckRoute;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class RoutesCest extends BaseAcceptanceCest
{
    private User $user;
    private User $user2;
    private Manager $manager;
    private Driver $driver;
    private Fleet $fleet;
    private Truck $truck;
    private Route $route;
    private Route $route2;

    private function setUp(): void
    {
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

        $this->manager = new Manager([
            'user_id' => $this->user->id
        ]);
        $this->manager->save();

        $this->driver = new Driver([
            'user_id' => $this->user2->id,
            'license_category' => 'A',
            'gender' => 'M',
            'commission_percent' => 12.5,
        ]);
        $this->driver->save();

        $this->fleet = new Fleet([
            'name' => 'Frota Teste',
            'manager_id' => $this->manager->id
        ]);
        $this->fleet->save();

        $truckBrand = new TruckBrand([
            'name' => 'Volvo'
        ]);
        $truckBrand->save();

        $this->truck = new Truck([
            'truck_brand_id' => $truckBrand->id,
            'model' => '113h',
            'color' => 'branco',
            'plate' => 'ABC4B67',
            'fleet_id' => $this->fleet->id,
            'driver_id' => $this->driver->id
        ]);
        $this->truck->save();

        $this->route = new Route([
            'start_address' => 'Guarapuava',
            'arrival_address' => 'São Paulo',
            'distance' => 270,
            'value' => 3000
        ]);
        $this->route->save();

        $this->route2 = new Route(['start_address' => 'Pitanga',
        'arrival_address' => 'Paranaguá',
        'distance' => 130,
        'value' => 2000
        ]);
        $this->route2->save();
    }

    public function seeMyRoutes(AcceptanceTester $page): void
    {
        $this->setUp();

        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route->id
        ]);
        $truckRoute->save();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id . '/trucks/' . $this->truck->id);

        $page->see($this->route->start_address);
        $page->see($this->route->arrival_address);

        $page->dontSee($this->route2->start_address);
        $page->dontSee($this->route2->arrival_address);
    }

    public function addRoute(AcceptanceTester $page): void
    {
        $this->setUp();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id . '/trucks/' . $this->truck->id);

        $page->dontSee($this->route->start_address);
        $page->dontSee($this->route->arrival_address);

        $page->click('Rotas');

        $page->see($this->route->start_address);
        $page->see($this->route->arrival_address);

        $page->see($this->route2->start_address);
        $page->see($this->route2->arrival_address);

        $page->click('#add_route_1');
        $page->click('#add_route_2');

        $page->click('#submit_routes');

        $page->see('Rotas atualizadas com sucesso!');

        // Rotas já na tela do caminhão
        $page->see($this->route->start_address);
        $page->see($this->route->arrival_address);

        $page->see($this->route2->start_address);
        $page->see($this->route2->arrival_address);
    }

    public function removeRoute(AcceptanceTester $page): void
    {
        $this->setUp();

        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route->id
        ]);
        $truckRoute->save();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id . '/trucks/' . $this->truck->id);

        $page->see($this->route->start_address);
        $page->see($this->route->arrival_address);

        $page->dontSee($this->route2->start_address);
        $page->dontSee($this->route2->arrival_address);

        $page->click('Rotas');

        $page->see($this->route->start_address);
        $page->see($this->route->arrival_address);

        $page->see($this->route2->start_address);
        $page->see($this->route2->arrival_address);

        $page->click('#remove_route_1');

        $page->click('#submit_routes');

        $page->see('Rotas removidas com sucesso!');

        $page->dontSee($this->route->start_address);
        $page->dontSee($this->route->arrival_address);

        $page->dontSee($this->route2->start_address);
        $page->dontSee($this->route2->arrival_address);
    }

    public function moveRoute(AcceptanceTester $page): void
    {
        $this->setUp();

        $truckRoute = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route->id
        ]);
        $truckRoute->save();

        $truckRoute2 = new TruckRoute([
            'truck_id' => $this->truck->id,
            'route_id' => $this->route2->id
        ]);
        $truckRoute2->save();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id . '/trucks/' . $this->truck->id . '/routes');

        $page->seeElement("//li[1][@data-id='{$this->route->id}']");
        $page->seeElement("//li[2][@data-id='{$this->route2->id}']");

        $page->click('#move_up_2');

        $page->seeElement("//li[1][@data-id='{$this->route2->id}']");
        $page->seeElement("//li[2][@data-id='{$this->route->id}']");
    }
}
