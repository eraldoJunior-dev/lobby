<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumThreadController extends Controller
{
    public function create(Request $request): View
    {
        $selected = $request->filled('jogo') ? Game::where('slug', $request->query('jogo'))->value('id') : null;

        return view('forums.create', [
            'games' => Game::orderBy('title')->get(['id', 'title']),
            'selected' => old('game_id', $selected),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'title' => ['required', 'string', 'max:140'],
            'body' => ['required', 'string', 'max:5000'],
        ], [
            'game_id.required' => __('Escolha o fórum do jogo.'),
            'game_id.exists' => __('Esse jogo não existe.'),
            'title.required' => __('Dê um título ao tópico.'),
            'title.max' => __('O título pode ter no máximo 140 caracteres.'),
            'body.required' => __('Escreva a mensagem do tópico.'),
            'body.max' => __('A mensagem pode ter no máximo 5000 caracteres.'),
        ]);

        $thread = $request->user()->threads()->create($data);

        return redirect()->route('threads.show', $thread)->with('status', __('Tópico criado.'));
    }

    public function show(ForumThread $thread): View
    {
        $thread->load(['game', 'user:id,name,username,avatar_path', 'replies.user:id,name,username,avatar_path']);

        return view('forums.thread', ['thread' => $thread]);
    }

    public function reply(Request $request, ForumThread $thread): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ], [
            'body.required' => __('Escreva sua resposta.'),
            'body.max' => __('A resposta pode ter no máximo 3000 caracteres.'),
        ]);

        $reply = $thread->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        if ($thread->user) \App\Support\LobbyNotifications::send($thread->user, $request->user(), 'reply', $request->user()->name.' respondeu ao seu tópico.', route('threads.show', $thread, false).'#reply-'.$reply->id);

        return redirect()->to(route('threads.show', $thread).'#reply-'.$reply->id)->with('status', __('Resposta enviada.'));
    }

    /** Só quem abriu o tópico marca/desmarca como resolvido. */
    public function solve(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless($thread->user_id === $request->user()->id, 403);

        $thread->update(['is_solved' => ! $thread->is_solved]);

        return redirect()->route('threads.show', $thread);
    }
}
