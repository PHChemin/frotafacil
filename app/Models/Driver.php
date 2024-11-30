<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $license_category
 * @property string $gender
 * @property float $commission_percent
 *
 *
 * */


class Driver extends Model
{
    protected static string $table = 'drivers';
    protected static array $columns = [
        'user_id',
        'license_category',
        'gender',
        'commission_percent'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validates(): void
    {
        if (Manager::exists(['user_id' => $this->user_id])) {
            $this->addError('user_id', 'Este usuário já está associado a um manager.');
        }
    }
}
