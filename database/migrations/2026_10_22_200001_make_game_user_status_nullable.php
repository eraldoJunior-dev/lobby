<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Favoritar ou avaliar um jogo não obriga a escolher "jogando / zerado / quero jogar".
        Schema::table('game_user', function (Blueprint $table) {
            $table->string('status', 12)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('game_user', function (Blueprint $table) {
            $table->string('status', 12)->default('backlog')->change();
        });
    }
};
