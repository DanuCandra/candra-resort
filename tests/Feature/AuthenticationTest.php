<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_register_and_is_redirected_to_guest_dashboard(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Candra Guest',
            'email' => 'guest@example.com',
            'phone' => '0812-3456-7890',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
        ]);

        $response->assertRedirect(route('guest.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'guest@example.com',
            'phone' => '6281234567890',
            'role' => UserRole::Guest->value,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('logout'), false)
            ->assertSee('Keluar');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_each_active_role_is_redirected_to_its_dashboard_after_login(): void
    {
        foreach ([
            [UserRole::Guest, 'guest.dashboard'],
            [UserRole::Receptionist, 'receptionist.dashboard'],
            [UserRole::Owner, 'owner.dashboard'],
        ] as [$role, $routeName]) {
            $user = User::factory()->create([
                'email' => $role->value.'@example.com',
                'username' => 'test-'.$role->value,
                'role' => $role,
                'password' => 'Secret123',
            ]);

            $response = $this->post(route('login.store'), [
                'login' => $user->email,
                'password' => 'Secret123',
            ]);

            $response->assertRedirect(route($routeName));
            $this->assertAuthenticatedAs($user);
            $this->post(route('logout'));
        }
    }

    public function test_login_accepts_username_for_staff(): void
    {
        $user = User::factory()->receptionist()->create([
            'username' => 'frontdesk',
            'password' => 'Secret123',
        ]);

        $response = $this->post(route('login.store'), [
            'login' => 'frontdesk',
            'password' => 'Secret123',
        ]);

        $response->assertRedirect(route('receptionist.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('Secret123'),
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'Secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
