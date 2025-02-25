<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

/**
 * @property int $id
 * @property int $truck_id
 * @property int $route_id
 *
 *
 * */

class TruckRoute extends Model
{
    protected static string $table = 'trucks_routes';
    protected static array $columns = [
        'truck_id',
        'route_id',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function validates(): void
    {
        if (!Validations::notEmpty('truck_id', $this)) {
            return;
        }

        if (!Validations::notEmpty('route_id', $this)) {
            return;
        }

        if (!Truck::findById($this->truck_id)) {
            $this->addError('truck_id', 'does not exist!');
            return;
        }

        if (!Route::findById($this->route_id)) {
            $this->addError('route_id', 'does not exist!');
            return;
        }
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
}
