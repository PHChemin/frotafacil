<?php

namespace Tests\Unit\Models\Drivers;

use App\Models\Driver;
use App\Models\Manager;
use App\Models\User;
use Tests\TestCase;

class DriverTest extends TestCase
{
    private User $user1;
    private User $user2;
    private Driver $driver;
    private User $managerUser;
    private Manager $manager;

    public function setUp(): void
    {
        parent::setUp();

        // Criar usuários base
        $this->user1 = new User([
            'cpf' => '12345678901',
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password123',
        ]);
        $this->user1->save();

        $this->user2 = new User([
            'cpf' => '10987654321',
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => 'password456',
        ]);
        $this->user2->save();

        // Criar um driver associado ao user1
        $this->driver = new Driver([
            'user_id' => $this->user1->id,
        ]);
        $this->driver->save();

        $this->managerUser = new User([
            'name' => 'Manager Name',
            'email' => 'manager@example.com',
            'cpf' => '12345678910',
            'password' => 'password123'
        ]);
        $this->managerUser->save();

        $this->manager = new Manager(['user_id' => $this->managerUser->id]);
        $this->manager->save();
    }

    public function test_should_create_new_driver(): void
    {
        $this->assertCount(1, Driver::all());
        $this->assertEquals($this->user1->id, $this->driver->user_id);
    }

    public function test_all_should_return_all_drivers(): void
    {
        $newDriver = new Driver([
            'user_id' => $this->user2->id,
        ]);
        $newDriver->save();

        $drivers = [$this->driver->id, $newDriver->id];
        $all = [];

        foreach (Driver::all() as $driver) {
            $all[] = $driver->id;
        }

        $this->assertCount(2, $all);
        $this->assertEquals($drivers, $all);
    }

    public function test_destroy_should_remove_the_driver(): void
    {
        $this->driver->destroy();
        $this->assertCount(0, Driver::all());
    }

    public function test_driver_should_be_associated_with_user(): void
    {
        $this->assertEquals($this->user1->id, $this->driver->user()->get()->id);
    }

    public function test_should_fail_if_user_is_already_a_manager(): void
    {
        $driver = new Driver(['user_id' => $this->manager->user()->get()->id]);

        $driver->validates();

        $this->assertTrue($driver->hasErrors());
        $this->assertTrue(in_array('user_id Usuário já associado a um gestor!', $driver->getErrors()));
    }

    public function test_validation_should_fail_for_missing_user_id(): void
    {
        $driver = new Driver(['user_id' => null]);

        $driver->validates();

        $this->assertTrue($driver->hasErrors());
        $this->assertTrue(in_array('user_id O user_id não pode ser nulo.', $driver->getErrors()));
    }

    public function test_find_by_user_id_should_return_the_driver(): void
    {
        $foundDriver = Driver::findBy(['user_id' => $this->user1->id]);
        $this->assertNotNull($foundDriver);
        $this->assertEquals($this->driver->id, $foundDriver->id);
    }

    public function test_find_by_user_id_should_return_null(): void
    {
        $foundDriver = Driver::findBy(['user_id' => 9999]);
        $this->assertNull($foundDriver);
    }

    public function test_should_update_driver_details(): void
    {
        $this->driver->license_category = 'B';
        $this->driver->commission_percent = 12.5;
        $this->driver->save();

        $updatedDriver = Driver::findById($this->driver->id);

        $this->assertEquals('B', $this->driver->license_category);
        $this->assertEquals(12.5, $this->driver->commission_percent);
    }
}
