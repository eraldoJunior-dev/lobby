<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostReactionController extends Controller
{
    /**
     * Liga/desliga uma reação do usuário na publicação.
     */
    public function store(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(ReactionType::class)],
        ]);

        $user = $request->user();
        $match = ['user_id' => $user->id, 'type' => $data['type']];

        if (! $post->reactions()->where($match)->delete()) {
            $post->reactions()->firstOrCreate($match);
            if ($post->user) \App\Support\LobbyNotifications::send($post->user, $user, 'reaction', $user->name.' reagiu à sua publicação.', route('home', [], false).'#post-'.$post->id);
        }

        if ($request->wantsJson()) {
            $post->load('reactions');

            return response()->json(['reactions' => $post->reactionSummary($user->id)]);
        }

        return redirect()->to(url()->previous(route('home')).'#post-'.$post->id);
    }
}
