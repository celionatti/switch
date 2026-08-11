<?php

declare(strict_types=1);

namespace App\Models;

use Switch\Database\ORM\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'name',
        'email',
        'password',
    ];
}
