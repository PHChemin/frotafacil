<?php

namespace App\Models;

use App\Models\TruckRoute;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\BelongsToMany;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

/**
 * @property int $id
 * @property string $start_address
 * @property string $arrival_address
 * @property float $distance
 * @property float $value
 *
 *
 * */

class Route extends Model
{
    protected static string $table = 'routes';
    protected static array $columns = [
        'start_address',
        'arrival_address',
        'distance',
        'value',
    ];

    public function trucksRoutes(): HasMany
    {
        return $this->hasMany(TruckRoute::class, 'route_id');
    }

    public function validates(): void
    {
        Validations::notEmpty('start_address', $this);
        Validations::notEmpty('arrival_address', $this);
        Validations::notEmpty('distance', $this);
        Validations::notEmpty('value', $this);
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
