<?php

declare(strict_types=1);

namespace App\Models;

use Switch\Database\ORM\Model;
use Switch\Foundation\Auth\Access\AuthorizableTrait;
use Switch\Foundation\Auth\AuthenticatableInterface;
use Switch\Foundation\Auth\Passwordless\HasPasswordlessAuth;

class User extends Model implements AuthenticatableInterface
{
    use HasPasswordlessAuth;
    use AuthorizableTrait;

    protected string $table = 'users';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
    ];
}
