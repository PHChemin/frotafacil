<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * @property int $id
 * @property string $cpf
 * @property string $encrypted_password
 * @property string|null $name
 * @property string|null $email
 *
 *
 * */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = [
        'cpf',
        'encrypted_password',
        'name',
        'email'
    ];

    protected array $errors = [];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function isManager(): bool
    {
        return Manager::findBy(['user_id' => $this->id]) !== null;
    }

    public function isDriver(): bool
    {
        return Driver::findBy(['user_id' => $this->id]) !== null;
    }

    public function manager(): ?Manager
    {
        return Manager::findBy(['user_id' => $this->id]);
    }

    public function driver(): ?Driver
    {
        return Driver::findBy(['user_id' => $this->id]);
    }

    public function validates(): void
    {
        Validations::notEmpty('cpf', $this);
        Validations::notEmpty('password', $this);
        Validations::uniqueness('cpf', $this);

        // TODO Validate cpf number
    }

    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password == null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }

    public static function findByCpf(string $cpf): User | null
    {
        return User::findBy(['cpf' => $cpf]);
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

    public function __set(string $property, mixed $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'password' &&
            $this->newRecord() &&
            $value !== null && $value !== ''
        ) {
            $this->encrypted_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }
}
