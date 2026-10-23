<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostSaveController extends Controller
{
    /**
     * Salva/remove a publicação dos itens salvos do usuário.
     */
    public function store(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $changes = $post->savedBy()->toggle($request->user()->id);

        if ($request->wantsJson()) {
            return response()->json(['saved' => count($changes['attached']) > 0]);
        }

        return redirect()->to(url()->previous(route('home')).'#post-'.$post->id);
    }
}
