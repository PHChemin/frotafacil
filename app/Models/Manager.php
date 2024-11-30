<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property int $user_id
 *
 *
 * */

class Manager extends Model
{
    protected static string $table = 'managers';
    protected static array $columns = ['user_id'];

    protected array $errors = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validates(): void
    {
        if ($this->user_id == 0) {
            $this->addError('user_id', 'O user_id não pode ser nulo.');
            return;
        }

        $drivers = Driver::all();

        foreach ($drivers as $driver) {
            if ($driver->user_id === $this->user_id) {
                $this->addError('user_id', 'Este usuário já está associado a um driver.');
                return;
            }
        }
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[] = "{$attribute} {$message}";
    }

    /**
    *@return string[] List of error messages, each as a string.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
