<?php

declare(strict_types=1);

namespace App\Models;

use Switch\Database\ORM\Model;
use Switch\Database\ORM\SoftDeletes;
use Switch\Foundation\Flow\HasAuditTrail;
use Switch\Foundation\Flow\HasFlow;
use Switch\Foundation\Flow\StateMachine;

class Post extends Model
{
    use HasFlow, HasAuditTrail, SoftDeletes;

    protected string $table = 'posts';
    protected string $primaryKey = 'id';
    protected bool $softDeletes = true;

    protected array $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'status',
        'tags',
        'is_featured',
    ];

    protected array $casts = [
        'tags' => 'json',
        'is_featured' => 'bool',
    ];

    /**
     * Define the finite state machine flow for Post status transitions.
     */
    public static function flow(): StateMachine
    {
        return StateMachine::define('status')
            ->states(['draft', 'published', 'archived'])
            ->initial('draft')
            ->allow('publish', from: 'draft', to: 'published')
            ->allow('archive', from: 'published', to: 'archived')
            ->allow('draft', from: 'archived', to: 'draft');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', '=', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', '=', true);
    }
}
