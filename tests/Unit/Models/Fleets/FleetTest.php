<?php

namespace Tests\Unit\Models\Fleets;

use App\Models\Fleet;
use App\Models\Manager;
use App\Models\User;
use ReflectionClass;
use Tests\TestCase;

use function PHPUnit\Framework\assertFalse;

class FleetTest extends TestCase
{
    private Fleet $fleet;
    private User $user;

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

        $manager = new Manager(['user_id' => $this->user->id]);
        $manager->save();

        $this->fleet = new Fleet(['name' => 'Fleet of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet->save();
    }

    // Registro da Relação
    public function test_should_create_a_new_fleet(): void
    {
        $fleet = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $this->assertTrue($fleet->save());
        $this->assertCount(2, Fleet::all());
    }

    // Teste de Listagem
    public function test_should_return_all_fleets(): void
    {
        $fleets[] = $this->fleet;
        $fleets[] = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleets[1]->save();

        $all = Fleet::all();
        $this->assertCount(2, $all);
        $this->assertEquals($fleets, $all);
    }

    // Remoção da Relação
    public function test_destroy_should_delete_the_fleet(): void
    {
        $fleet2 = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet2->save();
        $this->assertCount(2, Fleet::all());

        $fleet2->destroy();
        $this->assertCount(1, Fleet::all());
    }

    // Teste deletando o usuário e vendo se deleta a frota
    public function test_should_delete_fleet_if_user_deleted(): void
    {
        $user2 = new User([
            'cpf' => '12355578901',
            'name' => 'Manager Test 2',
            'email' => 'manager2@example.com',
            'password' => 'password123'
        ]);
        $user2->save();

        $manager2 = new Manager(['user_id' => $user2->id]);
        $manager2->save();

        $fleet2 = new Fleet(['name' => 'Fleet of manager 2', 'manager_id' => $manager2->id]);
        $fleet2->save();

        $this->assertCount(2, Fleet::all());

        $user2->destroy();

        $this->assertCount(1, Fleet::all());
    }

    public function test_set_name(): void
    {
        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet->name = 'New fleet name';
        $this->assertEquals('New fleet name', $fleet->name);
    }

    // Validação
    // Nome vazio
    public function test_should_return_error_if_name_is_empty(): void
    {
        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet->name = '';

        $this->assertFalse($fleet->isValid());
        $this->assertFalse($fleet->save());
        $this->assertFalse($fleet->hasErrors());

        $this->assertEquals('name cannot be empty!', $fleet->getErrors()[0]);
    }

    // Manager inexistente
    public function test_should_return_error_if_managerid_not_exist(): void
    {
        $fleet = new Fleet(['name' => 'Fleet 2', 'manager_id' => 100]);
        $fleet->save();

        $this->assertFalse($fleet->save());
        $this->assertEquals('manager_id does not exist!', $fleet->getErrors()[0]);
    }

    // Não deixar colocar um id inválido (Já usado)
    // Caso id = null, o banco completará com um id correto
    // Caso id já usado o banco não cria
    // Se você criar e salvar, depois tentar alterar o id para um que já exista o banco não deixa
    public function test_should_not_create_fleet_if_id_is_duplicated(): void
    {
        /** @var \App\Models\Fleet $fleet */
        $fleet = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet->id = 1;
        $fleet->save();

        $this->assertFalse($fleet->save());
        $this->assertCount(1, Fleet::all());
        $this->assertEquals('id has already been taken!', $fleet->getErrors()[0]);
    }

    public function test_set_id(): void
    {
        $fleet = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet->id = 10;
        $this->assertEquals(10, $fleet->id);
    }

    public function test_find_by_id_should_return_the_fleet(): void
    {
        $fleet2 = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet3 = $this->user->manager()->fleets()->new(['name' => 'Fleet 3']);
        $fleet4 = $this->user->manager()->fleets()->new(['name' => 'Fleet 4']);

        $fleet2->save();
        $fleet3->save();
        $fleet4->save();

        $this->assertEquals($fleet2, Fleet::findById($fleet2->id));
        $this->assertEquals($fleet3, Fleet::findById($fleet3->id));
        $this->assertNotEquals($fleet2, Fleet::findById($fleet4->id));
    }

    public function test_find_by_id_should_return_null(): void
    {
        $fleet2 = $this->user->manager()->fleets()->new(['name' => 'Fleet 2']);
        $fleet2->save();

        $this->assertNull(Fleet::findById(10));
    }
}
