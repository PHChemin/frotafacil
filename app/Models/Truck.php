<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

/**
 * @property int $id
 * @property int $truck_brand_id
 * @property string $model
 * @property string $color
 * @property string $plate
 * @property int $fleet_id
 * @property int $driver_id
 *
 *
 * */

class Truck extends Model
{
    protected static string $table = 'trucks';
    protected static array $columns = [
        'truck_brand_id',
        'model',
        'color',
        'plate',
        'fleet_id',
        'driver_id',
    ];

    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class, 'fleet_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function truckBrand(): BelongsTo
    {
        return $this->belongsTo(TruckBrand::class, 'truck_brand_id');
    }

    public function trucksRoutes(): HasMany
    {
        return $this->hasMany(TruckRoute::class, 'truck_id');
    }

    /**
     * ------------------- VALIDATIONS & ERRORS ----------------------
     */

    public function validates(): void
    {
        Validations::notEmpty('truck_brand_id', $this);
        Validations::notEmpty('model', $this);
        Validations::notEmpty('color', $this);
        Validations::notEmpty('plate', $this);
        Validations::notEmpty('fleet_id', $this);
        Validations::notEmpty('driver_id', $this);

        Validations::uniqueness(['plate'], $this);
        $this->driverExists();
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute] = "{$attribute} {$message}";
    }

    /**
    *@return string[] List of error messages, each as a string.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function translateError(string | null $error): string
    {
        if ($error === null) {
            return '';
        }

        $translations = [
            'has already been taken!' => 'Esta placa já está em uso!',
            'cannot be empty!' => 'O campo placa não pode estar vazio!',
        ];
        $errorMessage = preg_replace('/^\w+\s/', '', $error);

        return str_replace(array_keys($translations), array_values($translations), $errorMessage);
    }

    private function driverExists(): bool
    {
        if (Driver::exist($this->driver_id)) {
            return true;
        }

        $this->addError('driver_id', 'does not exist!');
        return false;
    }
}
