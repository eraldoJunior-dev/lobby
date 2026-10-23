<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(80, '')->value();

        $forums = Game::query()
            ->withCount('threads')
            ->when($search !== '', function (Builder $q) use ($search) {
                $like = '%'.$search.'%';

                $q->where(fn (Builder $w) => $w
                    ->where('title', 'like', $like)
                    ->orWhereHas('threads', fn (Builder $t) => $t->where('title', 'like', $like))
                );
            })
            ->orderByDesc('threads_count')
            ->orderByDesc('members_count')
            ->orderBy('title')
            ->get();

        return view('forums.index', [
            'forums' => $forums,
            'search' => $search,
            'stats' => [
                'forums' => Game::count(),
                'threads' => ForumThread::count(),
                'replies' => ForumReply::count(),
                'solved' => ForumThread::where('is_solved', true)->count(),
            ],
        ]);
    }

    public function show(Game $game): View
    {
        $threads = $game->threads()
            ->with('user:id,name,username,avatar_path')
            ->withCount('replies')
            ->orderByDesc('votes')
            ->orderByDesc('id')
            ->simplePaginate(15);

        return view('forums.show', ['game' => $game, 'threads' => $threads]);
    }
}
