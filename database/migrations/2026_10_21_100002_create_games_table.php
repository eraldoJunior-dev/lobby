<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('slug', 140)->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('genre', 40)->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedSmallInteger('hours_to_beat')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('cover_from', 7)->default('#4c3a7a');
            $table->string('cover_to', 7)->default('#1f1830');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
