<?php

namespace Tests\Unit\Controllers;

use App\Models\Driver;
use App\Models\User;

class DriversControllerTest extends ControllerTestCase
{
    private User $user1;
    private Driver $driver;

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

        $this->driver = new Driver([
            'user_id' => $this->user1->id,
        ]);
        $this->driver->save();

        $_SESSION['user']['id'] = $this->user1->id;
    }

    public function test_index(): void
    {
        $response = $this->get(action: 'index', controllerName: 'App\Controllers\DriversController');

        $this->assertStringContainsString('Página Inicial Motoristas', $response);
    }
}
