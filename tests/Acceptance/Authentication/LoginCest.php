<?php

namespace Tests\Acceptance\Authentication;

use App\Models\Manager;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class LoginCest extends BaseAcceptanceCest
{
    public function loginSuccessfully(AcceptanceTester $page): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $user->save();
        (new Manager([
            'user_id' => $user->id
        ]))->save();

        $page->amOnPage('/login');

        $page->fillField('user[cpf]', $user->cpf);
        $page->fillField('user[password]', $user->password);

        $page->click('Entrar');

        $page->see('Login realizado com sucesso!');
        $page->seeInCurrentUrl('/manager/fleets');
    }

    public function loginUnsuccessfully(AcceptanceTester $page): void
    {
        $page->amOnPage('/login');

        $page->fillField('user[cpf]', '11111111111');
        $page->fillField('user[password]', 'wrong_password');

        $page->click('Entrar');

        $page->see('Cpf e/ou senha inválidos!');
        $page->seeInCurrentUrl('/login');
    }
}
