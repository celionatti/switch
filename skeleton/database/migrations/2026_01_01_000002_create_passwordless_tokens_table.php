<?php

declare(strict_types=1);

use Switch\Database\Migration\Migration;
use Switch\Database\Schema\Blueprint;
use Switch\Database\Schema\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->create('passwordless_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token')->unique();
            $table->string('type', 20)->default('login');
            $table->text('payload')->nullable();
            $table->datetime('expires_at');
            $table->datetime('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropIfExists('passwordless_tokens');
    }
};
