<?php

namespace App\Providers;

use App\Models\ForumThread;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dados da coluna da direita das páginas novas (jogos, guias, fóruns…).
        View::composer('partials.rail', function ($view) {
            $me = auth()->user();

            $view->with([
                'railTrending' => ForumThread::with('game:id,title')->orderByDesc('votes')->orderByDesc('id')->limit(4)->get(),
                'railPeople' => $me
                    ? User::whereKeyNot($me->id)
                        ->whereNotIn('id', $me->following()->pluck('users.id'))
                        ->latest('id')
                        ->limit(4)
                        ->get(['id', 'name', 'username', 'avatar_path'])
                    : collect(),
                'railNews' => NewsItem::latest('published_at')->limit(4)->get(),
            ]);
        });
    }
}
