<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsernameGenerator
{
    /**
     * Gera um @usuario único a partir do primeiro nome (ex.: "Ana Beatriz" → "ana", "ana2"…).
     */
    public static function generate(string $name): string
    {
        $base = Str::slug(Str::before(trim($name), ' '), '');
        $base = Str::limit($base !== '' ? $base : 'jogador', 20, '');

        $candidate = $base;
        $n = 1;

        while (DB::table('users')->where('username', $candidate)->exists()) {
            $candidate = $base.++$n;
        }

        return $candidate;
    }
}
