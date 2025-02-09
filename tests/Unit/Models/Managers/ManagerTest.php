<?php

namespace Tests\Unit\Models\Managers;

use App\Models\Driver;
use App\Models\Manager;
use App\Models\User;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    private User $user1;
    private User $user2;
    private Manager $manager;
    private User $driverUser;
    private Driver $driver;

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

        // Criar um manager associado ao user1
        $this->manager = new Manager([
            'user_id' => $this->user1->id,
        ]);
        $this->manager->save();

        $this->driverUser = new User([
            'name' => 'Driver Name',
            'email' => 'driver@example.com',
            'cpf' => '12345678910',
            'password' => 'password123'
        ]);
        $this->driverUser->save();

        $this->driver = new Driver(['user_id' => $this->driverUser->id]);
        $this->driver->save();
    }

    public function test_should_create_new_manager(): void
    {
        $this->assertCount(1, Manager::all());
        $this->assertEquals($this->user1->id, $this->manager->user_id);
    }

    public function test_all_should_return_all_managers(): void
    {
        $newManager = new Manager([
            'user_id' => $this->user2->id,
        ]);
        $newManager->save();

        $managers = [$this->manager->id, $newManager->id];
        $all = [];

        foreach (Manager::all() as $manager) {
            $all[] = $manager->id;
        }

        $this->assertCount(2, $all);
        $this->assertEquals($managers, $all);
    }

    public function test_destroy_should_remove_the_manager(): void
    {
        $this->manager->destroy();
        $this->assertCount(0, Manager::all());
    }

    public function test_manager_should_be_associated_with_user(): void
    {
        $this->assertEquals($this->user1->id, $this->manager->user()->get()->id);
    }

    public function test_should_fail_if_user_is_already_a_driver(): void
    {
        $manager = new Manager(['user_id' => $this->driver->user()->get()->id]);

        $manager->validates();

        $this->assertTrue($manager->hasErrors());
        $this->assertTrue(in_array('user_id Este usuário já está associado a um driver.', $manager->getErrors()));
    }

    public function test_validation_should_fail_for_missing_user_id(): void
    {
        $manager = new Manager(['user_id' => null]);

        $manager->validates();

        $this->assertTrue($manager->hasErrors());
        $this->assertTrue(in_array('user_id O user_id não pode ser nulo.', $manager->getErrors()));
    }

    public function test_find_by_user_id_should_return_the_manager(): void
    {
        $foundManager = Manager::findBy(['user_id' => $this->user1->id]);
        $this->assertNotNull($foundManager);
        $this->assertEquals($this->manager->id, $foundManager->id);
    }

    public function test_find_by_user_id_should_return_null(): void
    {
        $foundManager = Manager::findBy(['user_id' => 9999]);
        $this->assertNull($foundManager);
    }
}
