<?php

namespace Tests\Acceptance\Trucks;

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

class TrucksCest extends BaseAcceptanceCest
{
    private User $user;
    private User $user2;
    private Manager $manager;
    private Driver $driver;
    private Fleet $fleet;

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

        $truckBrand2 = new TruckBrand([
            'name' => 'Scania'
        ]);
        $truckBrand2->save();
    }

    public function successfullyCreateTruck(AcceptanceTester $page): void
    {
        $this->setUp();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id);

        $page->click('Adicionar Caminhão');
        $page->waitForText('Novo caminhão');

        $page->selectOption('#truck_brand', 'Volvo');

        $page->fillField('#truck_model', 'FH 16');
        $page->fillField('#truck_color', 'Vermelho');
        $page->fillField('#truck_plate', 'ABC1234');

        $page->fillField('#driver_cpf', $this->user2->cpf);
        $page->click('Buscar');
        $page->waitForText('Motorista encontrado e disponível!');

        $page->click('Adicionar');

        $page->see('Caminhão registrado com sucesso!');
    }

    public function unsucessfullyCreateTruck(AcceptanceTester $page): void
    {
        $this->setUp();

        $page->login($this->user->cpf, $this->user->password);

        $page->amOnPage('/manager/fleets/' . $this->fleet->id);

        $page->click('Adicionar Caminhão');
        $page->waitForText('Novo caminhão');

        $page->fillField('#truck_model', 'FH 16');
        $page->fillField('#truck_color', 'Vermelho');
        $page->fillField('#truck_plate', 'ABC1234');

        $page->fillField('#driver_cpf', '');
        $page->click('Buscar');
        $page->waitForText('Por favor, informe um CPF.');
        $page->see('Por favor, informe um CPF.');

        $page->fillField('#driver_cpf', '111');
        $page->click('Buscar');
        $page->waitForText('CPF inválido.');
        $page->see('CPF inválido.');

        $page->fillField('#driver_cpf', '12345678955');
        $page->click('Buscar');
        $page->waitForText('Nenhum usuário encontrado com esse CPF.');
        $page->see('Nenhum usuário encontrado com esse CPF.');

        $page->fillField('#driver_cpf', $this->user->cpf);
        $page->click('Buscar');
        $page->waitForText('Este usuário não é um motorista.');
        $page->see('Este usuário não é um motorista.');

        $page->click('Adicionar');

        $page->see('Existem dados incorretos! Por favor verifique!');
        $page->see('O campo cpf do motorista contém erros!');
    }
}
