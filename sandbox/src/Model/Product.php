<?php

declare(strict_types=1);

namespace App\Model;

use Switch\Database\ORM\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = ['name', 'price', 'in_stock'];
    protected array $casts = ['price' => 'float', 'in_stock' => 'bool'];
}
