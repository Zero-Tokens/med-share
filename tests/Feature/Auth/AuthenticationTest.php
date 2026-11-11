<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('auth/login')
                ->has('canResetPassword'),
            );
    }

    public function test_public_registration_is_disabled()
    {
        $this->assertFalse(Route::has('register'));
        $this->assertFalse(Route::has('register.store'));

        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Pessoa',
            'email' => 'pessoa@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertGuest();
    }

    public function test_passkey_login_is_disabled()
    {
        $this->assertFalse(Features::canManagePasskeys());

        $passkeyRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_contains($route->uri(), 'passkey'));

        $this->assertCount(0, $passkeyRoutes);
    }

    public function test_authenticated_users_are_redirected_away_from_the_login_screen()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_email_and_password_are_required()
    {
        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => '',
                'password' => '',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'O campo e-mail é obrigatório.',
                'password' => 'O campo senha é obrigatório.',
            ]);

        $this->assertGuest();
    }

    public function test_invalid_credentials_show_a_clear_message()
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'E-mail ou senha incorretos.',
            ]);

        $this->assertGuest();
    }

    public function test_unknown_email_gets_the_same_message_as_a_wrong_password()
    {
        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'ninguem@example.com',
                'password' => 'password',
            ])
            ->assertSessionHasErrors([
                'email' => 'E-mail ou senha incorretos.',
            ]);

        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_with_two_factor_enabled_are_redirected_to_two_factor_challenge()
    {
        $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]);

        $user = User::factory()->withTwoFactor()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.login'));
        $response->assertSessionHas('login.id', $user->id);
        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_users_are_rate_limited()
    {
        $user = User::factory()->create();

        RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();
    }
}
