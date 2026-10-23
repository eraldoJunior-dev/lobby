<?php

namespace Tests\Feature;

use App\Models\ForumThread;
use App\Models\Game;
use App\Models\Guide;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    private function game(string $title, string $genre = 'RPG', array $extra = []): Game
    {
        return Game::create(array_merge([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'genre' => $genre,
            'year' => 2020,
            'rating' => 4.5,
            'members_count' => 10,
        ], $extra));
    }

    public function test_guests_are_redirected_from_every_new_page(): void
    {
        foreach (['/jogos', '/guias', '/foruns', '/noticias', '/chat', '/perfil', '/configuracoes', '/buscar'] as $url) {
            $this->get($url)->assertRedirect('/entrar');
        }
    }

    public function test_games_page_lists_filters_and_sorts(): void
    {
        $this->game('Alpha Quest', 'RPG', ['members_count' => 5, 'rating' => 3.0]);
        $this->game('Beta Run', 'Plataforma', ['members_count' => 50, 'rating' => 4.9]);
        $me = User::factory()->create();

        $this->actingAs($me)->get('/jogos')
            ->assertOk()
            ->assertSeeInOrder(['Beta Run', 'Alpha Quest'])
            ->assertSee('Plataforma');

        $this->actingAs($me)->get('/jogos?genero=RPG')
            ->assertOk()
            ->assertSee('Alpha Quest')
            ->assertDontSee('Beta Run</strong>', false);

        $this->actingAs($me)->get('/jogos?q=beta')
            ->assertOk()
            ->assertSee('Beta Run');

        $this->actingAs($me)->get('/jogos?ordem=az')
            ->assertOk()
            ->assertSeeInOrder(['Alpha Quest', 'Beta Run']);
    }

    public function test_guides_page_shows_progress_and_toggles_items(): void
    {
        $game = $this->game('Hollow Knight');
        $guide = Guide::create([
            'game_id' => $game->id, 'title' => 'Roteiro 112%', 'slug' => 'roteiro-112',
            'type' => '100', 'difficulty' => 4, 'hours_label' => '60-80 h',
        ]);
        $first = $guide->items()->create(['title' => 'Derrotar os Sonhadores', 'position' => 1]);
        $guide->items()->create(['title' => 'Libertar os Grubs', 'position' => 2]);
        $me = User::factory()->create();

        $this->actingAs($me)->get('/guias')
            ->assertOk()->assertSee('Roteiro 112%')->assertSee('0/2');

        $this->actingAs($me)->post("/guias/itens/{$first->id}/marcar")
            ->assertRedirect(route('guides.show', $guide).'#item-'.$first->id);

        $this->assertDatabaseHas('guide_item_user', ['user_id' => $me->id, 'guide_item_id' => $first->id]);

        $this->actingAs($me)->get('/guias')->assertSee('1/2');
        $this->actingAs($me)->get('/guias/roteiro-112')->assertOk()->assertSee('1 de 2 concluídos');

        $this->actingAs($me)->get('/guias?tipo=platina')->assertOk()->assertDontSee('Roteiro 112%');

        $this->actingAs($me)->post("/guias/itens/{$first->id}/marcar");
        $this->assertDatabaseMissing('guide_item_user', ['user_id' => $me->id, 'guide_item_id' => $first->id]);
    }

    public function test_forums_index_shows_stats_and_forum_page(): void
    {
        $game = $this->game('Celeste');
        $me = User::factory()->create();
        ForumThread::create(['game_id' => $game->id, 'user_id' => $me->id, 'title' => 'Dica do capítulo 5', 'body' => 'Texto', 'votes' => 3, 'is_solved' => true]);

        $this->actingAs($me)->get('/foruns')
            ->assertOk()->assertSee('Celeste')->assertSee('1 tópico');

        $this->actingAs($me)->get('/foruns/celeste')
            ->assertOk()->assertSee('Dica do capítulo 5')->assertSee('Resolvido');
    }

    public function test_user_can_create_thread_reply_and_mark_it_solved(): void
    {
        $game = $this->game('Hades');
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->post('/topicos', ['game_id' => $game->id, 'title' => 'Melhor build?', 'body' => 'Qual arma?'])
            ->assertRedirect();

        $thread = ForumThread::firstOrFail();
        $this->assertSame($me->id, $thread->user_id);
        $this->assertSame(0, $thread->votes);

        $this->actingAs($other)->post("/topicos/{$thread->id}/respostas", ['body' => 'Use o arco.'])->assertRedirect();
        $this->actingAs($me)->get("/topicos/{$thread->id}")
            ->assertOk()->assertSee('Use o arco.')->assertSee('1 resposta');

        // Só o autor resolve.
        $this->actingAs($other)->post("/topicos/{$thread->id}/resolver")->assertForbidden();
        $this->actingAs($me)->post("/topicos/{$thread->id}/resolver")->assertRedirect();
        $this->assertTrue($thread->fresh()->is_solved);
    }

    public function test_thread_requires_title_and_body(): void
    {
        $game = $this->game('Hades');

        $this->actingAs(User::factory()->create())
            ->post('/topicos', ['game_id' => $game->id, 'title' => '', 'body' => ''])
            ->assertSessionHasErrors(['title', 'body']);
    }

    public function test_thread_body_is_escaped(): void
    {
        $game = $this->game('Hades');
        $me = User::factory()->create();
        $thread = ForumThread::create(['game_id' => $game->id, 'user_id' => $me->id, 'title' => 'XSS', 'body' => '<script>alert(1)</script>']);

        $this->actingAs($me)->get("/topicos/{$thread->id}")
            ->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_news_page_filters_by_category(): void
    {
        NewsItem::create(['title' => 'Evento grande', 'source' => 'PC Gamer', 'category' => 'eventos', 'published_at' => now(), 'url' => 'https://example.com/a']);
        NewsItem::create(['title' => 'Lançamento novo', 'source' => 'DLCompare', 'category' => 'lancamentos', 'published_at' => now()->subDay()]);
        $me = User::factory()->create();

        $this->actingAs($me)->get('/noticias')
            ->assertOk()->assertSee('Evento grande')->assertSee('Lançamento novo')->assertSee('Ler na fonte');

        $this->actingAs($me)->get('/noticias?categoria=lancamentos')
            ->assertOk()->assertSee('Lançamento novo')->assertDontSee('Evento grande</h2>', false);
    }

    public function test_chat_page_says_it_is_beta_and_unavailable(): void
    {
        $this->actingAs(User::factory()->create())->get('/chat')
            ->assertOk()
            ->assertSee('Beta')
            ->assertSee('ainda não foi lançado')
            ->assertSee('não é possível conversar com outros perfis');
    }

    public function test_profile_shows_library_stats(): void
    {
        $me = User::factory()->create(['name' => 'Guest']);
        $a = $this->game('Hollow Knight', 'Metroidvania');
        $b = $this->game('Celeste', 'Plataforma');
        $me->library()->attach($a->id, ['status' => 'zerado', 'rating' => 4.5, 'hours_logged' => 27, 'review' => 'Ótimo', 'is_favorite' => true]);
        $me->library()->attach($b->id, ['status' => 'backlog', 'rating' => null, 'hours_logged' => 0, 'is_favorite' => false]);

        $this->actingAs($me)->get('/perfil')
            ->assertOk()
            ->assertSee('Guest')
            ->assertSee('Editar perfil')
            ->assertSee('Metroidvania')
            ->assertSee('Favoritos');
    }

    public function test_other_profile_respects_privacy(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create(['name' => 'Reservada']);
        $other->update(['settings' => ['privacy' => ['public_profile' => false]]]);

        $this->actingAs($me)->get('/perfil/'.$other->username)
            ->assertOk()->assertSee('Este perfil é privado')->assertDontSee('Editar perfil');

        $other->update(['settings' => ['privacy' => ['public_profile' => true, 'show_library' => false]]]);

        $this->actingAs($me)->get('/perfil/'.$other->username)
            ->assertOk()->assertSee('não compartilha a biblioteca')->assertSee('Seguir');
    }

    public function test_settings_tabs_render(): void
    {
        $me = User::factory()->create();

        foreach (['perfil', 'aparencia', 'privacidade', 'notificacoes', 'contas', 'dados'] as $tab) {
            $this->actingAs($me)->get('/configuracoes?aba='.$tab)->assertOk();
        }

        $this->actingAs($me)->get('/configuracoes?aba=inexistente')->assertOk()->assertSee('Editar perfil');
    }

    public function test_user_updates_profile(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => 'Novo Nome',
            'username' => 'NovoNome_1',
            'pronouns_choice' => 'ela/dela',
            'pronouns_custom' => '',
            'status' => 'Jogando Hades',
            'bio' => 'Gosto de roguelikes',
            'location' => 'Recife',
            'favorite_genre' => 'Roguelike',
            'website' => 'https://exemplo.com',
        ])->assertRedirect(route('settings.edit', ['aba' => 'perfil']));

        $me->refresh();
        $this->assertSame('Novo Nome', $me->name);
        $this->assertSame('novonome_1', $me->username);
        $this->assertSame('ela/dela', $me->pronouns);
        $this->assertSame('Recife', $me->location);
    }

    public function test_custom_pronouns_win_over_the_list(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => $me->name, 'username' => $me->username,
            'pronouns_choice' => 'ele/dele', 'pronouns_custom' => 'they/them',
        ])->assertSessionHasNoErrors();

        $this->assertSame('they/them', $me->fresh()->pronouns);
    }

    public function test_profile_update_validates_username_and_website(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => 'X', 'username' => $other->username, 'website' => 'não é um link',
        ])->assertSessionHasErrors(['username', 'website']);

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => 'X', 'username' => 'a b',
        ])->assertSessionHasErrors('username');
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');
        $me = User::factory()->create();

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => $me->name, 'username' => $me->username,
            'avatar' => UploadedFile::fake()->image('foto.png', 200, 200),
        ])->assertSessionHasNoErrors();

        $path = $me->fresh()->avatar_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($me)->put('/configuracoes/perfil', [
            'name' => $me->name, 'username' => $me->username, 'remove_avatar' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertNull($me->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_privacy_and_notification_preferences_are_saved(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->put('/configuracoes/privacidade', ['public_profile' => '1'])->assertRedirect();
        $me->refresh();
        $this->assertTrue($me->setting('privacy.public_profile'));
        $this->assertFalse($me->setting('privacy.show_library'));

        $this->actingAs($me)->put('/configuracoes/notificacoes', ['replies' => '1'])->assertRedirect();
        $me->refresh();
        $this->assertTrue($me->setting('notifications.replies'));
        $this->assertFalse($me->setting('notifications.news'));
        $this->assertTrue($me->setting('privacy.public_profile'));
    }

    public function test_user_can_export_data(): void
    {
        $me = User::factory()->create();

        $response = $this->actingAs($me)->get('/configuracoes/dados/exportar');

        $response->assertOk()->assertDownload('lobby-meus-dados.json');
        $this->assertStringContainsString($me->email, $response->streamedContent());
    }

    public function test_user_can_delete_account_with_password(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->delete('/configuracoes/conta', ['password' => 'errada'])
            ->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $me->id]);

        $this->actingAs($me)->delete('/configuracoes/conta', ['password' => 'password'])
            ->assertRedirect(route('landing'));
        $this->assertDatabaseMissing('users', ['id' => $me->id]);
        $this->assertGuest();
    }

    public function test_global_search_finds_games_threads_and_people(): void
    {
        $game = $this->game('Elden Ring');
        $me = User::factory()->create(['name' => 'Eldenfã Silva']);
        ForumThread::create(['game_id' => $game->id, 'user_id' => $me->id, 'title' => 'Regras de Elden Ring', 'body' => 'x']);

        $this->actingAs($me)->get('/buscar?q=elden')
            ->assertOk()->assertSee('Elden Ring')->assertSee('Regras de Elden Ring')->assertSee('Eldenfã Silva');

        $this->actingAs($me)->get('/buscar?q=e')->assertOk()->assertSee('pelo menos 2 letras');
    }

    public function test_sidebar_links_to_the_new_pages(): void
    {
        $this->actingAs(User::factory()->create())->get('/jogos')
            ->assertOk()
            ->assertSee(route('guides.index'), false)
            ->assertSee(route('forums.index'), false)
            ->assertSee(route('news.index'), false)
            ->assertSee(route('chat'), false)
            ->assertSee(route('profile.show'), false)
            ->assertSee(route('settings.edit'), false);
    }
}
