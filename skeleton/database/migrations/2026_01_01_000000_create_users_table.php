<?php

declare(strict_types=1);

use Switch\Database\Migration\Migration;
use Switch\Database\Schema\Blueprint;
use Switch\Database\Schema\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropIfExists('users');
    }
};
