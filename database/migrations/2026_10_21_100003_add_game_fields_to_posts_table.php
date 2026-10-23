<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
        });

        // No feed novo a publicação não tem título; as antigas continuam com o que já tinham.
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title', 120)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('game_id');
            $table->dropColumn('rating');
        });
    }
};
