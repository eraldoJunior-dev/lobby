<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Biblioteca pessoal: o que cada pessoa jogou, avaliou e favoritou.
        Schema::create('game_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('status', 12)->default('backlog'); // jogando | zerado | backlog
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedSmallInteger('hours_logged')->default(0);
            $table->text('review')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_user');
    }
};
