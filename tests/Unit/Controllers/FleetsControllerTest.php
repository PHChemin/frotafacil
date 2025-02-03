<?php

namespace Tests\Unit\Controllers;

use App\Models\Fleet;
use App\Models\User;
use App\Models\Manager;

class FleetsControllerTest extends ControllerTestCase
{
    private User $user;
    private Manager $manager;
    private Fleet $fleet;
    private Fleet $fleet2;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $this->user->save();

        $this->manager = new Manager(['user_id' => $this->user->id]);
        $this->manager->save();

        $this->fleet = new Fleet(['name' => 'Fleet of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet->save();

        $this->fleet2 = new Fleet(['name' => 'Fleet 2 of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet2->save();

        $_SESSION['user']['id'] = $this->user->id;
    }

    public function test_list_all_fleets(): void
    {
        $fleets = $this->user->manager()->fleets()->get();
        $response = $this->get(action: 'index', controllerName: 'App\Controllers\FleetsController');

        $this->assertStringContainsString('Frotas', $response);

        foreach ($fleets as $fleet) {
            /** @var \App\Models\Fleet $fleet */
            $this->assertMatchesRegularExpression("/{$fleet->name}/", $response);
        }
    }

    public function test_new_fleet(): void
    {
        $response = $this->get('new', 'App\Controllers\FleetsController');

        $this->assertStringContainsString('Nova frota', $response);
    }

    public function test_successfully_create_fleet(): void
    {
        $params = ['fleet'  => ['name' => 'Frota teste']];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\FleetsController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/Location: \/manager\/fleets/", $response);
    }

    public function test_unsuccessfully_create_fleet(): void
    {
        $params = ['fleet'  => ['name' => '']];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\FleetsController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/não pode estar vazio!/", $response);
    }

    public function test_edit_fleet(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();

        $response = $this->get(
            action: 'edit',
            controllerName: 'App\Controllers\FleetsController',
            params: ['fleet_id' => $fleet->id]
        );

        $this->assertMatchesRegularExpression("/Editar {$fleet->name}/", $response);

        $regex = '/<input\s+[^>]*type=[\'"]text[\'"][^>]*name=[\'"]fleet\[name\][\'"][^>]*>/i';
        $this->assertMatchesRegularExpression($regex, $response);
    }

    public function test_successfully_update_fleet(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();
        $params = ['fleet_id' => $fleet->id, 'fleet' => ['name' => $fleet->name]];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\FleetsController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/Location: \/manager\/fleets/", $response);
    }

    public function test_unsuccessfully_update_fleet(): void
    {
        $fleet = new Fleet(['name' => 'Frota 1', 'manager_id' => $this->user->manager()->id]);
        $fleet->save();
        $params = ['fleet_id' => $fleet->id, 'fleet' => ['name' => '']];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\FleetsController',
            params: $params
        );

        $this->assertMatchesRegularExpression("/não pode estar vazio!/", $response);
    }
}
