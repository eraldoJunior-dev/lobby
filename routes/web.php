<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumThreadController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameLibraryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostSaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SteamConnectionController;
use Illuminate\Support\Facades\Route;

// Página inicial pública (landing)
Route::view('/', 'landing')->name('landing');

// Idioma (PT-BR / US), vale também para quem ainda não entrou
Route::post('/idioma', [LocaleController::class, 'update'])->name('locale.update');

// Visitantes (quem já está logado é redirecionado para /inicio)
Route::middleware('guest')->group(function () {
    Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
    Route::post('/cadastro', [RegisterController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/entrar', [LoginController::class, 'create'])->name('login');
    Route::post('/entrar', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

// Área logada
Route::middleware('auth')->group(function () {
    Route::get('/inicio', HomeController::class)->name('home');
    Route::post('/minhas-plataformas', [\App\Http\Controllers\PlatformController::class, 'update'])->name('platforms.update');
    Route::get('/notificacoes', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificacoes/ler-todas', [\App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notificacoes/{id}/abrir', [\App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');

    Route::post('/publicacoes', [PostController::class, 'store'])->middleware('throttle:10,1')->name('posts.store');
    Route::delete('/publicacoes/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/publicacoes/{post}/reacoes', [PostReactionController::class, 'store'])->name('posts.react');
        Route::post('/publicacoes/{post}/salvar', [PostSaveController::class, 'store'])->name('posts.save');
        Route::post('/usuarios/{user}/seguir', [FollowController::class, 'store'])->name('users.follow');
    });

    // Jogos, guias, fóruns, notícias
    Route::get('/jogos', [GameController::class, 'index'])->name('games.index');
    Route::get('/jogos/{game:slug}', [GameController::class, 'show'])->name('games.show');
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/jogos/{game:slug}/status', [GameLibraryController::class, 'status'])->name('games.status');
        Route::post('/jogos/{game:slug}/favorito', [GameLibraryController::class, 'favorite'])->name('games.favorite');
        Route::post('/jogos/{game:slug}/horas', [GameLibraryController::class, 'hours'])->name('games.hours');
        Route::post('/jogos/{game:slug}/avaliacao', [GameLibraryController::class, 'review'])->name('games.review');
    });
    Route::get('/avatar/{user}', [AvatarController::class, 'show'])->name('avatar.show');

    Route::get('/guias', [GuideController::class, 'index'])->name('guides.index');
    Route::get('/guias/{guide:slug}', [GuideController::class, 'show'])->name('guides.show');
    Route::post('/guias/itens/{item}/marcar', [GuideController::class, 'toggle'])->middleware('throttle:120,1')->name('guides.toggle');

    Route::get('/foruns', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/foruns/novo-topico', [ForumThreadController::class, 'create'])->name('threads.create');
    Route::get('/foruns/{game:slug}', [ForumController::class, 'show'])->name('forums.show');
    Route::post('/topicos', [ForumThreadController::class, 'store'])->middleware('throttle:10,1')->name('threads.store');
    Route::get('/topicos/{thread}', [ForumThreadController::class, 'show'])->name('threads.show');
    Route::post('/topicos/{thread}/respostas', [ForumThreadController::class, 'reply'])->middleware('throttle:20,1')->name('threads.reply');
    Route::post('/topicos/{thread}/resolver', [ForumThreadController::class, 'solve'])->name('threads.solve');

    Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');

    // Chat (beta: ainda não lançado)
    Route::view('/chat', 'chat')->name('chat');

    // Perfil e configurações
    Route::get('/perfil/{user:username?}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/configuracoes', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::get('/configuracoes/steam/conectar', [SteamConnectionController::class, 'redirect'])->middleware('throttle:10,1')->name('steam.connect');
    Route::get('/configuracoes/steam/retorno', [SteamConnectionController::class, 'callback'])->middleware('throttle:10,1')->name('steam.callback');
    Route::delete('/configuracoes/steam', [SteamConnectionController::class, 'disconnect'])->name('steam.disconnect');
    Route::put('/configuracoes/perfil', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/configuracoes/aparencia', [SettingsController::class, 'updateAppearance'])->name('settings.appearance');
    Route::put('/configuracoes/privacidade', [SettingsController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::put('/configuracoes/notificacoes', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::get('/configuracoes/dados/exportar', [SettingsController::class, 'export'])->name('settings.export');
    Route::delete('/configuracoes/conta', [SettingsController::class, 'destroy'])->middleware('throttle:5,1')->name('settings.destroy');

    Route::get('/buscar', SearchController::class)->name('search');

    Route::post('/sair', [LoginController::class, 'destroy'])->name('logout');
});

// Suporte do Gubbo e painel administrativo (autorização validada no servidor).
Route::middleware('auth')->group(function () {
 Route::post('/suporte/chamados', [\App\Http\Controllers\SupportController::class,'create'])->middleware('throttle:5,1')->name('support.create');
 Route::get('/suporte/chamados', [\App\Http\Controllers\SupportController::class,'mine'])->name('support.mine');
 Route::get('/suporte/chamados/{id}', [\App\Http\Controllers\SupportController::class,'show'])->name('support.show');
 Route::post('/suporte/chamados/{id}/responder', [\App\Http\Controllers\SupportController::class,'reply'])->middleware('throttle:15,1')->name('support.reply');
 Route::get('/admin', [\App\Http\Controllers\SupportController::class,'dashboard'])->name('admin.dashboard');
 Route::post('/admin/chamados/{id}/status', [\App\Http\Controllers\SupportController::class,'status'])->name('admin.status');
 Route::post('/admin/usuarios/{user}/moderar', [\App\Http\Controllers\SupportController::class,'moderate'])->name('admin.moderate');
 Route::post('/admin/usuarios/{user}/cargo', [\App\Http\Controllers\SupportController::class,'roles'])->name('admin.roles');
});
