<?php

namespace App\Models;

use App\Services\TruckBrandAvatar;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name
 *
 * */

class TruckBrand extends Model
{
    protected static string $table = 'truck_brands';
    protected static array $columns = ['name'];

    protected array $errors = [];

    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class, 'truck_brand_id');
    }

    public function logo(): TruckBrandAvatar
    {
        return new TruckBrandAvatar($this);
    }
}
