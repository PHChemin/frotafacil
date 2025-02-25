<?php

namespace Database\Populate;

use App\Models\Route;

class RoutesPopulate
{
    public static function populate(): void
    {
        $route = new Route(['start_address' => 'Guarapuava', 'arrival_address' => 'Curitiba', 'distance' => 270, 'value' => 3000]);
        $route->save();

        $route2 = new Route(['start_address' => 'Curitiba', 'arrival_address' => 'Paranaguá', 'distance' => 130, 'value' => 2000]);
        $route2->save();

        $route3 = new Route(['start_address' => 'Paranaguá', 'arrival_address' => 'Guarapuava', 'distance' => 500, 'value' => 5000]);
        $route3->save();

        echo "Routes populated with 3 registers\n";
    }
}