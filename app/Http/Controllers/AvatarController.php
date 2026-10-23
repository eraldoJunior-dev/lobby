<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AvatarController extends Controller
{
    /**
     * Entrega a foto de perfil direto do storage. Assim ela aparece mesmo sem
     * rodar `php artisan storage:link` e sem depender do APP_URL.
     */
    public function show(User $user): Response
    {
        $path = $user->avatar_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
