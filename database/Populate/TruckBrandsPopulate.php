<?php

namespace Database\Populate;

use App\Models\TruckBrand;

class TruckBrandsPopulate
{
    public static function populate() : void 
    {
        $brands = [
            'Scania',
            'Volvo',
            'Mercedes-Benz',
            'Iveco',
            'DAF',
            'MAN',
            'Volkswagen'
        ];

        foreach ($brands as $brandName) {
            $truckBrand = new TruckBrand(['name' => $brandName]);
            $truckBrand->save();
        }

        echo "Truck brands popualted successfully.\n";
    }
}