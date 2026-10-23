<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Valor ilustrativo no protótipo (usado na ordenação "mais populares" e nos fóruns).
            $table->unsignedInteger('members_count')->default(0);
        });

        Schema::table('news_items', function (Blueprint $table) {
            $table->string('category', 20)->default('selecoes')->index();
            $table->text('summary')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropColumn(['category', 'summary']);
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('members_count');
        });
    }
};
