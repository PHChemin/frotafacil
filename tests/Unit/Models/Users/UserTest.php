<?php

namespace Tests\Unit\Models\Users;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user1;
    private User $user2;

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

        $this->user2 = new User([
            'cpf' => '10987654321',
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => 'password456',
        ]);
        $this->user2->save();
    }

    public function test_should_create_new_user(): void
    {
        $this->assertCount(2, User::all());
    }

    public function test_all_should_return_all_users(): void
    {
        $users = [$this->user1->id, $this->user2->id];
        $all = array_map(fn($user) => $user->id, User::all());

        $this->assertCount(2, $all);
        $this->assertEquals($users, $all);
    }

    public function test_destroy_should_remove_the_user(): void
    {
        $this->user1->destroy();
        $this->assertCount(1, User::all());
    }

    public function test_set_name(): void
    {
        $this->user1->name = 'Updated Name';
        $this->assertEquals('Updated Name', $this->user1->name);
    }

    public function test_set_email(): void
    {
        $this->user1->email = 'new_email@example.com';
        $this->assertEquals('new_email@example.com', $this->user1->email);
    }

    public function test_find_by_cpf_should_return_the_user(): void
    {
        $this->assertEquals($this->user1->id, User::findByCpf($this->user1->cpf)->id);
    }

    public function test_find_by_cpf_should_return_null(): void
    {
        $this->assertNull(User::findByCpf('00000000000'));
    }

    public function test_authenticate_should_return_true(): void
    {
        $this->assertTrue($this->user1->authenticate('password123'));
    }

    public function test_authenticate_should_return_false(): void
    {
        $this->assertFalse($this->user1->authenticate('wrongpassword'));
    }

    public function test_validation_should_fail_for_duplicate_cpf(): void
    {
        $duplicateUser = new User([
            'cpf' => $this->user1->cpf,
            'name' => 'Duplicate User',
            'email' => 'duplicate@example.com',
            'password' => 'password789',
        ]);

        $this->assertFalse($duplicateUser->save());
        $this->assertEquals('cpf has already been taken!', $duplicateUser->errors('cpf'));
    }

    public function test_validation_should_fail_for_missing_fields(): void
    {
        /** @var \App\Models\User $invalidUser */
        $invalidUser = new User(['cpf' => '', 'password' => '']);

        $this->assertFalse($invalidUser->save());
        $this->assertEquals('cpf cannot be empty!', $invalidUser->errors('cpf'));
        $this->assertEquals('password cannot be empty!', $invalidUser->errors('password'));
    }
}
