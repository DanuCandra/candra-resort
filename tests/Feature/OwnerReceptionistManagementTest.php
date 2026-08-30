<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OwnerReceptionistManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_create_and_update_receptionist_account(): void
    {
        $owner = User::factory()->owner()->create();

        $response = $this->actingAs($owner)->post(route('owner.receptionists.store'), [
            'name' => 'Front Office Test',
            'email' => 'front-office-test@example.com',
            'username' => 'frontoffice_test',
            'employee_code' => 'REC-TEST-01',
            'phone' => '0812-5555-6666',
            'is_active' => 1,
            'password' => 'Reception123',
            'password_confirmation' => 'Reception123',
        ]);

        $receptionist = User::where('email', 'front-office-test@example.com')->firstOrFail();
        $response->assertRedirect(route('owner.receptionists.show', $receptionist));
        $this->assertSame(UserRole::Receptionist, $receptionist->role);
        $this->assertSame('6281255556666', $receptionist->phone);
        $this->assertTrue(Hash::check('Reception123', $receptionist->password));
        $this->assertSame($owner->id, $receptionist->created_by);

        $this->actingAs($owner)->put(route('owner.receptionists.update', $receptionist), [
            'name' => 'Front Office Updated',
            'email' => $receptionist->email,
            'username' => $receptionist->username,
            'employee_code' => $receptionist->employee_code,
            'phone' => '0812-7777-8888',
            'is_active' => 1,
        ])->assertRedirect(route('owner.receptionists.show', $receptionist));

        $this->assertDatabaseHas('users', ['id' => $receptionist->id, 'name' => 'Front Office Updated', 'phone' => '6281277778888']);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $owner->id, 'module' => 'receptionists', 'event' => 'update']);
    }

    public function test_owner_can_deactivate_and_reset_receptionist_password(): void
    {
        $owner = User::factory()->owner()->create();
        $receptionist = User::factory()->receptionist()->create(['password' => 'OldPassword123']);

        $this->actingAs($owner)->post(route('owner.receptionists.toggle', $receptionist))->assertRedirect();
        $this->assertFalse($receptionist->fresh()->is_active);

        $this->actingAs($owner)->post(route('owner.receptionists.reset-password', $receptionist), [
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('NewPassword456', $receptionist->fresh()->password));
        $this->post(route('logout'));
        $this->post(route('login.store'), ['login' => $receptionist->email, 'password' => 'NewPassword456'])
            ->assertSessionHasErrors('login');
    }

    public function test_destroying_audited_receptionist_deactivates_instead_of_deleting(): void
    {
        $owner = User::factory()->owner()->create();
        $receptionist = User::factory()->receptionist()->create();
        $receptionist->auditLogs()->create([
            'event' => 'check_in',
            'module' => 'stays',
            'description' => 'Historical operation.',
            'created_at' => now(),
        ]);

        $this->actingAs($owner)->delete(route('owner.receptionists.destroy', $receptionist))->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $receptionist->id, 'is_active' => false, 'deleted_at' => null]);
    }

    public function test_receptionist_cannot_manage_receptionist_accounts(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->get(route('owner.receptionists.index'))->assertForbidden();
        $this->actingAs($receptionist)->post(route('owner.receptionists.store'), [])->assertForbidden();
    }
}
