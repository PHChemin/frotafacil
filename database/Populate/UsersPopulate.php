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
        $user1 = new User([
            'name' => 'Gestor exemplo',
            'email' => 'managerexample@example.com',
            'cpf' => '12345678901',
            'password' => 'password123'
        ]);
        $user1->save();

        $manager1 = new Manager(['user_id' => $user1->id]);
        $manager1->save();

        $numberOfUsers = 4;

        for ($i = 0; $i < $numberOfUsers; $i++) {
            $managerData = [
                'name' => 'Manager '. $i . ' Name',
                'email' => 'manager' . $i . '@example.com',
                'cpf' => '1234567890' . ($i + 2),
                'password' => 'password123'
            ];

            $managerUser = new User($managerData);
            $managerUser->save();

            $manager = new Manager(['user_id' => $managerUser->id]);
            $manager->save();
        }

        $driverData = [
            'name' => 'Driver Name',
            'email' => 'driver@example.com',
            'cpf' => '10987654321',
            'password' => 'password123'
        ];
        $driverUser = new User($driverData);
        $driverUser->save();

        $driver = new Driver(['user_id' => $driverUser->id, 'license_category' => 'E', 'gender' => 'M', 'commission_percent' => 14]);
        $driver->save();

        echo "Users populated successfully.\n";
    }
}