<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Perfil próprio (/perfil) ou de outra pessoa (/perfil/{usuario}). */
    public function show(Request $request, ?User $user = null): View
    {
        $me = $request->user();
        $user ??= $me;
        $isMe = $user->is($me);

        // Privacidade: o dono sempre vê tudo.
        $isPublic = $isMe || (bool) $user->setting('privacy.public_profile', true);
        $showLibrary = $isPublic && ($isMe || (bool) $user->setting('privacy.show_library', true));

        $library = $showLibrary ? $user->library()->get() : collect();

        $rated = $library->filter(fn ($g) => $g->pivot->rating !== null);
        $finished = $library->where('pivot.status', 'zerado')->count();
        $reviews = $library->filter(fn ($g) => filled($g->pivot->review))->count();

        $postsCount = $user->posts()->count();
        $threadsCount = $user->threads()->count();
        $repliesCount = $user->forumReplies()->count();

        $reputation = (int) $user->threads()->sum('votes')
            + Reaction::whereIn('post_id', $user->posts()->select('id'))->count();

        $xp = $postsCount + 2 * $threadsCount + $repliesCount + 3 * $finished + 2 * $reviews;

        return view('profile.show', [
            'profile' => $user,
            'isMe' => $isMe,
            'isPublic' => $isPublic,
            'showLibrary' => $showLibrary,
            'isFollowing' => ! $isMe && $me->following()->whereKey($user->id)->exists(),
            'followersCount' => $user->followers()->count(),
            'followingCount' => $user->following()->count(),
            'level' => 1 + intdiv($xp, 5),
            'reputation' => $reputation,
            'threadsCount' => $threadsCount,
            'ratedCount' => $rated->count(),
            'avgScore' => $rated->isNotEmpty() ? round($rated->avg(fn ($g) => (float) $g->pivot->rating), 1) : null,
            'finished' => $finished,
            'hours' => (int) $library->sum(fn ($g) => (int) $g->pivot->hours_logged),
            'backlog' => $library->where('pivot.status', 'backlog')->count(),
            'reviews' => $reviews,
            'topGenres' => $library->pluck('genre')->filter()->countBy()->sortDesc()->take(3),
            'favorites' => $library->filter(fn ($g) => (bool) $g->pivot->is_favorite)->take(8)->values(),
        ]);
    }
}
