<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_each_role_can_open_its_own_dashboard(): void
    {
        $guest = User::factory()->create();
        $receptionist = User::factory()->receptionist()->create();
        $owner = User::factory()->owner()->create();

        $this->actingAs($guest)->get(route('guest.dashboard'))->assertOk()->assertSee('Reservasi Terbaru');
        $this->actingAs($receptionist)->get(route('receptionist.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Operasional')
            ->assertSee('id="reception-command-center"', false)
            ->assertSee('Antrean Operasional')
            ->assertSee('Pusat Kerja Receptionist');
        $this->actingAs($owner)->get(route('owner.dashboard'))->assertOk()->assertSee('Business Overview');
    }

    public function test_guest_cannot_open_staff_dashboards(): void
    {
        $guest = User::factory()->create();

        $this->actingAs($guest)->get(route('receptionist.dashboard'))->assertForbidden();
        $this->actingAs($guest)->get(route('owner.dashboard'))->assertForbidden();
    }

    public function test_receptionist_cannot_open_owner_dashboard(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->get(route('owner.dashboard'))->assertForbidden();
    }

    public function test_owner_cannot_open_receptionist_operational_dashboard(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get(route('receptionist.dashboard'))->assertForbidden();
    }

    public function test_unauthenticated_user_is_sent_to_login(): void
    {
        $this->get(route('guest.dashboard'))->assertRedirect(route('login'));
    }
}
