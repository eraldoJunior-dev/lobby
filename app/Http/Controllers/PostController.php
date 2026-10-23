<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(StorePostRequest $request): RedirectResponse
    {
        $request->user()->posts()->create([
            'body' => $request->validated('body'),
            'game_id' => $request->validated('game_id'),
            'rating' => $request->validated('rating'),
            'contains_spoilers' => $request->boolean('contains_spoilers'),
        ]);

        return redirect()->route('home')->with('status', __('Publicação enviada!'));
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->user_id === $request->user()->id, 403);

        $post->delete();

        return redirect()->route('home')->with('status', __('Publicação removida.'));
    }
}
