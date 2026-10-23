<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_public(): void
    {
        $this->get('/')->assertOk()->assertSee('Criar conta');
    }

    public function test_register_and_login_pages_render(): void
    {
        $this->get('/cadastro')->assertOk();
        $this->get('/entrar')->assertOk();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/cadastro', [
            'name' => 'Maria Gamer',
            'email' => 'Maria@Example.com',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ]);

        $response->assertRedirect('/inicio');
        $this->assertAuthenticated();

        $user = User::where('email', 'maria@example.com')->firstOrFail();
        $this->assertNotSame('senha-segura', $user->password);
        $this->assertTrue(Hash::check('senha-segura', $user->password));
    }

    public function test_register_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/cadastro', [
            'name' => '',
            'email' => 'taken@example.com',
            'password' => 'curta',
            'password_confirmation' => 'diferente',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'senha-segura']);

        $this->post('/entrar', [
            'email' => $user->email,
            'password' => 'senha-segura',
        ])->assertRedirect('/inicio');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'senha-segura']);

        $this->from('/entrar')->post('/entrar', [
            'email' => $user->email,
            'password' => 'errada',
        ])->assertRedirect('/entrar')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_home_requires_authentication(): void
    {
        $this->get('/inicio')->assertRedirect('/entrar');
    }

    public function test_authenticated_user_sees_home_and_is_redirected_from_guest_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/inicio')->assertOk()->assertSee($user->name);
        $this->actingAs($user)->get('/entrar')->assertRedirect('/inicio');
        $this->actingAs($user)->get('/cadastro')->assertRedirect('/inicio');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/sair')->assertRedirect('/');

        $this->assertGuest();
    }
}
