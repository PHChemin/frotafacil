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
        $pdo = Database::getDatabaseConn();

        // Criar um gestor
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (cpf, encrypted_password, name, email) VALUES ('12345678901', '$password', 'Manager Name', 'manager@example.com')");
        $managerUserId = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO managers (user_id) VALUES ($managerUserId)");

        // Criar um motorista
        $pdo->exec("INSERT INTO users (cpf, encrypted_password, name, email) VALUES ('10987654321', '$password', 'Driver Name', 'driver@example.com')");
        $driverUserId = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO drivers (user_id) VALUES ($driverUserId)");

        echo "Users populated successfully.\n";
    }
}