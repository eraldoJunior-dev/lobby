<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->string('type', 20)->index(); // platina | 100 | conquistas
            $table->unsignedTinyInteger('difficulty')->default(3); // 1 a 5
            $table->string('hours_label', 20)->nullable(); // ex.: "60-80 h"
            $table->text('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('guide_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        // Progresso do checklist de cada usuário.
        Schema::create('guide_item_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guide_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'guide_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_item_user');
        Schema::dropIfExists('guide_items');
        Schema::dropIfExists('guides');
    }
};
