<?php

namespace Tests\Unit\Controllers;

use App\Models\Driver;
use App\Models\User;

class DriversProfileControllerTest extends ControllerTestCase
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

    public function test_show(): void
    {
        $response = $this->get(action: 'show', controllerName: 'App\Controllers\DriversProfileController');

        $this->assertStringContainsString('Meu Perfil', $response);
    }

    public function test_successfully_update_profile(): void
    {
        $params = [
            'driver_id' => $this->driver->id,
            'driver' => [
                'gender' => 'F',
                'license_category' => ['A', 'B',]
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\DriversProfileController',
            params: $params
        );

        $this->assertMatchesRegularExpression(
            "/Location: \/driver\/profile/",
            $response
        );
    }

    // Gender e license podem ser nulo então se enviar vazio OK!
    // Porém no front o botão é desabilidato, evitando que o usuário mande o formulário sem valor
    public function test_successfully_update_profile_with_empty_values(): void
    {
        $params = [
            'driver_id' => $this->driver->id,
            'driver' => [
                'gender' => '',
                'license_category' => []
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\DriversProfileController',
            params: $params
        );

        $this->assertMatchesRegularExpression(
            "/Location: \/driver\/profile/",
            $response
        );
    }
}
