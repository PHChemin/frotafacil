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
        if (Driver::exists(['user_id' => $this->user_id])) {
            $this->addError('user_id', 'Este usuário já está associado a um driver.');
        }
    }
}
