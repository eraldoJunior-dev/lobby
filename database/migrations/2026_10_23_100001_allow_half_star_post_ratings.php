<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep the decimal column to preserve existing half-star reviews.
    }
};
