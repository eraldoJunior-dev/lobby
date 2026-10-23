<?php

use App\Support\UsernameGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique();
        });

        // Quem já tinha conta ganha um @usuario automaticamente.
        DB::table('users')->orderBy('id')->get(['id', 'name'])->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'username' => UsernameGenerator::generate($user->name),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
