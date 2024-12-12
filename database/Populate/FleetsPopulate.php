<?php

namespace Database\Populate;

use App\Models\Fleet;
use App\Models\User;

class FleetsPopulate
{
    public static function populate(): void
    {
        $manager = User::findByCpf('12345678901')->manager();

        $numberOfFleets = 4;

        for ($i = 0; $i < $numberOfFleets; $i++) {
            $fleet = new Fleet(['name' => 'Frota ' . $i, 'manager_id' => $manager->id]);
            $fleet->save();
        }

        $manager2 = User::findByCpf('12345678902')->manager();

        $fleet2 = new Fleet(['name' => 'Frota do Manager ID2', 'manager_id' => $manager2->id]);
        $fleet2->save();

        echo "Fleets populated with $numberOfFleets registers\n";
    }
}