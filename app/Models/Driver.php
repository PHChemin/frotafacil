<?php

namespace App\Models;

use App\Services\DriverAvatar;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

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
        'commission_percent',
        'avatar_name'
    ];

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

        $managers = Manager::all();

        foreach ($managers as $manager) {
            if ($manager->user_id === $this->user_id) {
                $this->addError('user_id', 'Usuário já associado a um gestor!');
                return;
            }
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

    public function avatar(): DriverAvatar
    {
        return new DriverAvatar($this, ['extension' => ['png', 'jpg'], 'size' => 2 * 1024 * 1024]);
    }
}
