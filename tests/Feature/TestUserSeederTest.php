<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_user_can_log_in_and_reach_the_dashboard()
    {
        $this->seed(TestUserSeeder::class);

        $this->post(route('login.store'), [
            'email' => 'igor@gmail.com',
            'password' => '12345',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_running_the_seeder_twice_does_not_duplicate_the_user()
    {
        $this->seed(TestUserSeeder::class);
        $this->seed(TestUserSeeder::class);

        $this->assertSame(1, User::where('email', 'igor@gmail.com')->count());
    }

    public function test_seeder_does_nothing_in_production()
    {
        $this->app['env'] = 'production';

        $this->seed(TestUserSeeder::class);

        $this->assertDatabaseMissing('users', ['email' => 'igor@gmail.com']);
    }
}
