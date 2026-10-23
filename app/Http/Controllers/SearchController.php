<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\Game;
use App\Models\Guide;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /** Busca global (campo "Buscar em tudo" da coluna da direita). */
    public function __invoke(Request $request): View
    {
        $q = $request->string('q')->trim()->limit(80, '')->value();
        $like = '%'.$q.'%';
        $enough = mb_strlen($q) >= 2;

        return view('search', [
            'q' => $q,
            'enough' => $enough,
            'games' => $enough ? Game::where('title', 'like', $like)->orderByDesc('members_count')->limit(8)->get() : collect(),
            'guides' => $enough ? Guide::with('game')->where('title', 'like', $like)->limit(8)->get() : collect(),
            'threads' => $enough ? ForumThread::with('game:id,title')->where('title', 'like', $like)->orderByDesc('votes')->limit(8)->get() : collect(),
            'news' => $enough ? NewsItem::where('title', 'like', $like)->latest('published_at')->limit(8)->get() : collect(),
            'people' => $enough ? User::where(fn ($w) => $w->where('name', 'like', $like)->orWhere('username', 'like', $like))->limit(8)->get() : collect(),
        ]);
    }
}
