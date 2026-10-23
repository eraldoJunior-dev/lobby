<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Game;
use App\Models\NewsItem;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $me = $request->user();

        $tab = $request->query('aba') === 'seguindo' ? 'seguindo' : 'para-voce';
        $search = $request->string('q')->trim()->limit(80, '')->value();
        $activeGame = $request->filled('jogo') ? Game::find($request->integer('jogo')) : null;

        $followingIds = $me->following()->pluck('users.id');

        $posts = Post::query()
            ->with(['user:id,name,username', 'game', 'reactions:id,post_id,user_id,type'])
            ->withExists(['savedBy as saved_by_me' => fn (Builder $q) => $q->where('users.id', $me->id)])
            ->when($tab === 'seguindo', fn (Builder $q) => $q->where(
                fn (Builder $w) => $w->whereIn('user_id', $followingIds)->orWhere('user_id', $me->id)
            ))
            ->when($activeGame, fn (Builder $q) => $q->where('game_id', $activeGame->id))
            ->when($search !== '', function (Builder $q) use ($search) {
                $like = '%'.$search.'%';

                $q->where(fn (Builder $w) => $w
                    ->where('body', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhereHas('game', fn (Builder $g) => $g->where('title', 'like', $like))
                    ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $like)->orWhere('username', 'like', $like))
                );
            })
            ->latest('id')
            ->simplePaginate(10)
            ->withQueryString();

        $isFiltered = $activeGame !== null || $search !== '';

        return view('home', [
            'posts' => $posts,
            'tab' => $tab,
            'search' => $search,
            'activeGame' => $activeGame,
            'reactionTypes' => ReactionType::cases(),

            // Faixa de jogos recomendados: só na primeira página do feed sem filtros.
            'recommended' => ! $isFiltered && $posts->currentPage() === 1
                ? Game::orderByDesc('rating')->orderBy('title')->limit(6)->get()
                : collect(),

            'games' => Game::orderBy('title')->get(['id', 'title']),

            // Coluna da direita
            'trending' => Post::query()
                ->with(['game:id,title', 'user:id,name'])
                ->withCount('reactions')
                ->whereHas('reactions')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderByDesc('reactions_count')
                ->orderByDesc('id')
                ->limit(4)
                ->get(),

            'suggestions' => User::query()
                ->whereKeyNot($me->id)
                ->whereNotIn('id', $followingIds)
                ->latest('id')
                ->limit(4)
                ->get(['id', 'name', 'username']),

            'news' => NewsItem::latest('published_at')->limit(4)->get(),
        ]);
    }
}
