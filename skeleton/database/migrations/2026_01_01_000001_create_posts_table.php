<?php

declare(strict_types=1);

use Switch\Database\Migration\Migration;
use Switch\Database\Schema\Blueprint;
use Switch\Database\Schema\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('status')->default('draft');
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropIfExists('posts');
    }
};
