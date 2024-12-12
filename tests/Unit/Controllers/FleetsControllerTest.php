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

        $_SESSION['user']['id'] = $this->user->id;
    }

    public function test_index(): void
    {
        $response = $this->get('index', 'App\Controllers\FleetsController');

        $this->assertStringContainsString('Frotas', $response);
    }
}
