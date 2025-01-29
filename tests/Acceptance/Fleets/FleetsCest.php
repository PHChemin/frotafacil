<?php

namespace Tests\Acceptance\Fleets;

use App\Models\Fleet;
use App\Models\Manager;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class FleetsCest extends BaseAcceptanceCest
{
    public function seeMyFleets(AcceptanceTester $page): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $user->save();

        $manager = new Manager([
            'user_id' => $user->id
        ]);
        $manager->save();

        $fleet = new Fleet([
            'name' => 'Frota Teste',
            'manager_id' => $manager->id
        ]);
        $fleet->save();

        $page->login($user->cpf, $user->password);

        $page->amOnPage('/manager/fleets');

        $page->see($fleet->name);
    }
}
