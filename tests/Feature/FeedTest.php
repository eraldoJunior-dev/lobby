<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_shows_posts_from_all_users(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create(['name' => 'Outro Jogador']);
        Post::factory()->for($other)->create(['body' => 'Guia do chefão final']);

        $this->actingAs($me)->get('/inicio')
            ->assertOk()
            ->assertSee('Guia do chefão final')
            ->assertSee('Outro Jogador');
    }

    public function test_feed_shows_empty_state(): void
    {
        $this->actingAs(User::factory()->create())->get('/inicio')
            ->assertOk()
            ->assertSee('Ainda não há publicações');
    }

    public function test_legacy_post_title_is_still_shown(): void
    {
        Post::factory()->create(['title' => 'Título antigo', 'body' => 'Corpo antigo']);

        $this->actingAs(User::factory()->create())->get('/inicio')
            ->assertOk()
            ->assertSee('Título antigo');
    }

    public function test_new_users_get_a_unique_username(): void
    {
        $a = User::factory()->create(['name' => 'Ana Beatriz']);
        $b = User::factory()->create(['name' => 'Ana Souza']);

        $this->assertSame('ana', $a->username);
        $this->assertSame('ana2', $b->username);
    }

    public function test_user_can_publish_a_post_with_game_and_rating(): void
    {
        $user = User::factory()->create();
        $game = Game::create(['title' => 'Hades', 'slug' => 'hades']);

        $this->actingAs($user)->post('/publicacoes', [
            'body' => 'Cada run me deixa com vontade de mais uma.',
            'game_id' => $game->id,
            'rating' => 5,
            'contains_spoilers' => '1',
        ])->assertRedirect('/inicio');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'game_id' => $game->id,
            'rating' => 5,
            'contains_spoilers' => true,
            'body' => 'Cada run me deixa com vontade de mais uma.',
        ]);
    }

    public function test_user_can_publish_a_post_without_game(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/publicacoes', ['body' => 'Alguém joga hoje à noite?'])
            ->assertRedirect('/inicio');

        $this->assertDatabaseHas('posts', ['user_id' => $user->id, 'game_id' => null, 'rating' => null]);
    }

    public function test_post_validation(): void
    {
        $this->actingAs(User::factory()->create())->post('/publicacoes', [
            'body' => '',
            'game_id' => 999,
        ])->assertSessionHasErrors(['body', 'game_id']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_rating_requires_a_game(): void
    {
        $this->actingAs(User::factory()->create())->post('/publicacoes', [
            'body' => 'Ótimo!',
            'rating' => 5,
        ])->assertSessionHasErrors(['game_id']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $game = Game::create(['title' => 'Hades', 'slug' => 'hades']);

        $this->actingAs(User::factory()->create())->post('/publicacoes', [
            'body' => 'Ótimo!', 'game_id' => $game->id, 'rating' => 6,
        ])->assertSessionHasErrors(['rating']);
    }

    public function test_guest_cannot_publish(): void
    {
        $this->post('/publicacoes', ['body' => 'y'])->assertRedirect('/entrar');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_post_content_is_escaped(): void
    {
        Post::factory()->create(['body' => '<script>alert(1)</script>']);

        $this->actingAs(User::factory()->create())->get('/inicio')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_user_can_delete_only_own_post(): void
    {
        $me = User::factory()->create();
        $mine = Post::factory()->for($me)->create();
        $theirs = Post::factory()->create();

        $this->actingAs($me)->delete("/publicacoes/{$theirs->id}")->assertForbidden();
        $this->assertModelExists($theirs);

        $this->actingAs($me)->delete("/publicacoes/{$mine->id}")->assertRedirect('/inicio');
        $this->assertModelMissing($mine);
    }

    public function test_feed_is_paginated(): void
    {
        Post::factory()->count(12)->create();

        $this->actingAs(User::factory()->create())->get('/inicio')
            ->assertOk()
            ->assertSee('Mais antigas');
    }

    public function test_following_tab_shows_only_followed_users_and_my_own_posts(): void
    {
        $me = User::factory()->create();
        $friend = User::factory()->create();
        $stranger = User::factory()->create();

        $me->following()->attach($friend->id);

        Post::factory()->for($friend)->create(['body' => 'post do amigo']);
        Post::factory()->for($me)->create(['body' => 'post meu']);
        Post::factory()->for($stranger)->create(['body' => 'post de desconhecido']);

        $this->actingAs($me)->get('/inicio?aba=seguindo')
            ->assertOk()
            ->assertSee('post do amigo')
            ->assertSee('post meu')
            ->assertDontSee('post de desconhecido');
    }

    public function test_search_filters_posts(): void
    {
        Post::factory()->create(['body' => 'Fui atrás do boss secreto']);
        Post::factory()->create(['body' => 'Receita de bolo']);

        $this->actingAs(User::factory()->create())->get('/inicio?q=boss')
            ->assertOk()
            ->assertSee('boss secreto')
            ->assertDontSee('Receita de bolo');
    }

    public function test_game_filter_shows_only_that_games_posts(): void
    {
        $hades = Game::create(['title' => 'Hades', 'slug' => 'hades']);
        $celeste = Game::create(['title' => 'Celeste', 'slug' => 'celeste']);

        Post::factory()->create(['game_id' => $hades->id, 'body' => 'sobre o hades']);
        Post::factory()->create(['game_id' => $celeste->id, 'body' => 'sobre o celeste']);

        $this->actingAs(User::factory()->create())->get("/inicio?jogo={$hades->id}")
            ->assertOk()
            ->assertSee('sobre o hades')
            ->assertDontSee('sobre o celeste');
    }

    public function test_recommended_games_show_on_first_page_only_without_filters(): void
    {
        Game::create(['title' => 'Hollow Knight', 'slug' => 'hollow-knight', 'rating' => 4.7]);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/inicio')->assertSee('Recomendados para você');
        $this->actingAs($user)->get('/inicio?q=algo')->assertDontSee('Recomendados para você');
    }

    public function test_user_can_toggle_a_reaction(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->postJson("/publicacoes/{$post->id}/reacoes", ['type' => 'fire'])
            ->assertOk()
            ->assertJsonPath('reactions.fire.count', 1)
            ->assertJsonPath('reactions.fire.active', true)
            ->assertJsonPath('reactions.like.count', 0);

        $this->actingAs($user)->postJson("/publicacoes/{$post->id}/reacoes", ['type' => 'fire'])
            ->assertOk()
            ->assertJsonPath('reactions.fire.count', 0)
            ->assertJsonPath('reactions.fire.active', false);

        $this->assertDatabaseCount('reactions', 0);
    }

    public function test_reaction_type_is_validated(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/publicacoes/'.Post::factory()->create()->id.'/reacoes', ['type' => 'xyz'])
            ->assertStatus(422);
    }

    public function test_reaction_without_javascript_redirects_back_to_the_post(): void
    {
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from('/inicio')
            ->post("/publicacoes/{$post->id}/reacoes", ['type' => 'like'])
            ->assertRedirect("/inicio#post-{$post->id}");
    }

    public function test_user_can_toggle_save(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->postJson("/publicacoes/{$post->id}/salvar")
            ->assertOk()->assertJson(['saved' => true]);
        $this->assertTrue($user->savedPosts()->whereKey($post->id)->exists());

        $this->actingAs($user)->postJson("/publicacoes/{$post->id}/salvar")
            ->assertOk()->assertJson(['saved' => false]);
        $this->assertFalse($user->savedPosts()->whereKey($post->id)->exists());
    }

    public function test_user_can_follow_and_unfollow(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->postJson("/usuarios/{$other->id}/seguir")
            ->assertOk()->assertJson(['following' => true]);
        $this->assertTrue($me->following()->whereKey($other->id)->exists());

        $this->actingAs($me)->postJson("/usuarios/{$other->id}/seguir")
            ->assertOk()->assertJson(['following' => false]);
        $this->assertFalse($me->following()->whereKey($other->id)->exists());
    }

    public function test_user_cannot_follow_themselves(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->postJson("/usuarios/{$me->id}/seguir")->assertStatus(422);
        $this->assertDatabaseCount('follows', 0);
    }

    public function test_followed_users_are_not_suggested_again(): void
    {
        $me = User::factory()->create();
        $followed = User::factory()->create(['name' => 'Seguido Qualquer']);
        $new = User::factory()->create(['name' => 'Novato Sugerido']);
        $me->following()->attach($followed->id);

        $this->actingAs($me)->get('/inicio')
            ->assertOk()
            ->assertSee('Novato Sugerido')
            ->assertDontSee('Seguido Qualquer');
    }

    public function test_interactions_require_login(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->post("/publicacoes/{$post->id}/reacoes", ['type' => 'like'])->assertRedirect('/entrar');
        $this->post("/publicacoes/{$post->id}/salvar")->assertRedirect('/entrar');
        $this->post("/usuarios/{$user->id}/seguir")->assertRedirect('/entrar');
    }

    public function test_trending_lists_most_reacted_posts(): void
    {
        $hot = Post::factory()->create(['body' => 'publicação quente da semana']);
        Post::factory()->create(['body' => 'publicação sem reações']);

        foreach (User::factory()->count(2)->create() as $u) {
            $hot->reactions()->create(['user_id' => $u->id, 'type' => 'like']);
        }

        $this->actingAs(User::factory()->create())->get('/inicio')
            ->assertOk()
            ->assertSee('Em alta na comunidade')
            ->assertSee('2 reações');
    }
}
