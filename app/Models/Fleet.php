<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\HasMany;
use Lib\Validations;

/**
 * @property int $id
 * @property string $name
 * @property int $manager_id
 *
 *
 * */


class Fleet extends Model
{
    protected static string $table = 'fleets';
    protected static array $columns = [
        'name',
        'manager_id',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class, 'fleet_id');
    }

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        $this->managerExists();
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

    private function managerExists(): bool
    {
        if (Manager::exist($this->manager_id)) {
            return true;
        }

        $this->addError('manager_id', 'does not exist!');
        return false;
    }
}
