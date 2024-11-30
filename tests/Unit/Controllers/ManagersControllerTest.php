<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\Manager;

class ManagersControllerTest extends ControllerTestCase
{
    private User $user1;
    private Manager $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->user1 = new User([
            'cpf' => '12345678901',
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password123',
        ]);
        $this->user1->save();

        $this->manager = new Manager([
            'user_id' => $this->user1->id,
        ]);
        $this->manager->save();

        $_SESSION['user']['id'] = $this->user1->id;
    }

    public function test_index(): void
    {
        $response = $this->get('index', 'App\Controllers\ManagersController');

        $this->assertStringContainsString('Página Inicial Gestores', $response);
    }
}
