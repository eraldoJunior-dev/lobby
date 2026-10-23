<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalfStarRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_half_star_rating_is_saved_without_rounding(): void
    {
        $user = User::factory()->create();
        $game = Game::create(['title' => 'Hades', 'slug' => 'hades']);
        foreach (range(1, 10) as $step) {
            $score = $step / 2;
            $this->actingAs($user)->post('/publicacoes', [
                'body' => 'Minha avaliação', 'game_id' => $game->id, 'rating' => $score,
            ])->assertSessionHasNoErrors();
            $this->assertSame($score, (float) $user->posts()->latest('id')->first()->rating);
        }
    }

    public function test_invalid_rating_steps_and_limits_are_rejected(): void
    {
        $user = User::factory()->create();
        $game = Game::create(['title' => 'Hades', 'slug' => 'hades']);
        foreach ([0, 0.25, 2.7, 5.5] as $score) {
            $this->actingAs($user)->post('/publicacoes', [
                'body' => 'Minha avaliação', 'game_id' => $game->id, 'rating' => $score,
            ])->assertSessionHasErrors('rating');
        }
        $this->assertDatabaseCount('posts', 0);
    }
}
