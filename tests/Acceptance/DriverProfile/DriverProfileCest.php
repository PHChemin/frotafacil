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
}
