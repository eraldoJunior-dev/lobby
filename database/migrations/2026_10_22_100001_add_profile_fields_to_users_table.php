<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pronouns', 40)->nullable();
            $table->string('status', 80)->nullable();
            $table->text('bio')->nullable();
            $table->string('location', 80)->nullable();
            $table->string('favorite_genre', 40)->nullable();
            $table->string('website')->nullable();
            $table->string('avatar_path')->nullable();
            $table->json('settings')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pronouns', 'status', 'bio', 'location', 'favorite_genre', 'website', 'avatar_path', 'settings']);
        });
    }
};
