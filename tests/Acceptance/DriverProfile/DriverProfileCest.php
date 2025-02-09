<?php

namespace Tests\Acceptance\DriverProfile;

use App\Models\Driver;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class DriverProfileCest extends BaseAcceptanceCest
{
    public function seeMyInfo(AcceptanceTester $page): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $user->save();

        $driver = new Driver([
            'user_id' => $user->id,
            'gender' => 'M',
            'license_category' => 'A,B,C,D'
        ]);
        $driver->save();

        $page->login($user->cpf, $user->password);

        $page->amOnPage('/driver/profile');

        $page->see($user->name);
        $page->see($user->email);
        $page->see($driver->gender);
    }

    public function testUploadAvatar(AcceptanceTester $page): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $user->save();

        $driver = new Driver([
            'user_id' => $user->id,
            'gender' => 'M',
            'license_category' => 'A,B,C,D'
        ]);
        $driver->save();

        $page->login($user->cpf, $user->password);

        $page->amOnPage('/driver/profile');

        $page->seeElement('#open_file_input');
        $page->attachFile('#image_preview_input', 'avatar_test.png');

        $page->click('#image_preview_submit');
        $page->see('Avatar atualizado com sucesso!');
    }

    public function testUpdateProfile(AcceptanceTester $page): void
    {
        $user = new User([
            'cpf' => '12345678901',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $user->save();

        $driver = new Driver([
            'user_id' => $user->id,
            'gender' => 'M',
            'license_category' => 'A,B,C,D'
        ]);
        $driver->save();

        $page->login($user->cpf, $user->password);

        $page->amOnPage('/driver/profile');

        $page->checkOption('#gender_m');
        $page->checkOption('#cat_B');

        $page->click('#submit_update_profile');
        $page->see('Perfil editado com sucesso!');
    }
}
