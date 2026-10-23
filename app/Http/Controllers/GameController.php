<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    /** @var list<string> */
    private const SORTS = ['populares', 'nota', 'recentes', 'az'];

    public function index(Request $request): View
    {
        $genre = $request->string('genero')->trim()->limit(40, '')->value();
        $search = $request->string('q')->trim()->limit(80, '')->value();
        $sort = in_array($request->query('ordem'), self::SORTS, true) ? $request->query('ordem') : 'populares';

        $query = Game::query()
            ->when($genre !== '', fn ($q) => $q->where('genre', $genre))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', '%'.$search.'%'));

        match ($sort) {
            'nota' => $query->orderByDesc('rating')->orderBy('title'),
            'recentes' => $query->orderByDesc('year')->orderBy('title'),
            'az' => $query->orderBy('title'),
            default => $query->orderByDesc('members_count')->orderBy('title'),
        };

        return view('games.index', [
            'games' => $query->get(),
            'genres' => Game::query()
                ->whereNotNull('genre')
                ->selectRaw('genre, count(*) as total')
                ->groupBy('genre')
                ->orderByDesc('total')
                ->orderBy('genre')
                ->pluck('genre'),
            'genre' => $genre,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function show(Request $request, Game $game): View
    {
        $me = $request->user();

        $mine = $me->library()->whereKey($game->id)->first()?->pivot;

        $entries = $game->libraryEntries()->get();
        $rated = $entries->filter(fn ($u) => $u->pivot->rating !== null);

        // Distribuição das notas dos membros (0,5 a 5 estrelas).
        $histogram = collect(range(1, 10))->mapWithKeys(
            fn ($n) => [(string) ($n / 2) => $rated->filter(fn ($u) => (float) $u->pivot->rating === $n / 2)->count()]
        );

        return view('games.show', [
            'game' => $game,
            'steamFacts' => \App\Support\GameFacts::get($game),
            'completionTimes' => \App\Support\GameFacts::completionTimes($game),
            'mine' => $mine,
            'ratedCount' => $rated->count(),
            'memberAvg' => $rated->isNotEmpty() ? round($rated->avg(fn ($u) => (float) $u->pivot->rating), 1) : null,
            'histogram' => $histogram,
            'reviews' => $entries
                ->filter(fn ($u) => filled($u->pivot->review))
                ->sortByDesc(fn ($u) => $u->pivot->updated_at)
                ->take(10)
                ->values(),
            'similar' => Game::query()
                ->whereKeyNot($game->id)
                ->when($game->genre, fn ($q) => $q->where('genre', $game->genre))
                ->orderByDesc('rating')
                ->limit(4)
                ->get(),
            'guides' => $game->guides()->orderBy('id')->get(),
            'threadsCount' => $game->threads()->count(),
            'postsCount' => $game->posts()->count(),
        ]);
    }
}
