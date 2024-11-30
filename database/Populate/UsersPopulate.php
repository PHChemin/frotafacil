<?php

namespace Database\Populate;

use App\Models\User;
use App\Models\Manager;
use App\Models\Driver;
use Core\Database\Database;

class UsersPopulate
{
    public static function populate(): void
    {
        $managerData = [
            'name' => 'Manager Name',
            'email' => 'manager@example.com',
            'cpf' => '12345678901',
            'password' => 'password123'
        ];
        $managerUser = new User($managerData);
        $managerUser->save();

        $manager = new Manager(['user_id' => $managerUser->id]);
        $manager->save();

        $driverData = [
            'name' => 'Driver Name',
            'email' => 'driver@example.com',
            'cpf' => '10987654321',
            'password' => 'password123'
        ];
        $driverUser = new User($driverData);
        $driverUser->save();

        $driver = new Driver(['user_id' => $driverUser->id]);
        $driver->save();

        echo "Users populated successfully.\n";
    }
}