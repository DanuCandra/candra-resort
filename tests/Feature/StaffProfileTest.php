<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_and_owner_can_open_their_profile_page(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $owner = User::factory()->owner()->create();

        $this->actingAs($receptionist)
            ->get(route('receptionist.profile.edit'))
            ->assertOk()
            ->assertSee('id="staff-profile-page"', false)
            ->assertSee('Profil Saya')
            ->assertSee(route('receptionist.profile.update'));

        $this->actingAs($owner)
            ->get(route('owner.profile.edit'))
            ->assertOk()
            ->assertSee('id="staff-profile-page"', false)
            ->assertSee(route('owner.profile.update'));
    }

    public function test_staff_can_update_contact_data_and_profile_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/old.jpg', 'old-avatar');
        $receptionist = User::factory()->receptionist()->create([
            'avatar_path' => 'profile-photos/old.jpg',
        ]);

        $response = $this->actingAs($receptionist)->put(route('receptionist.profile.update'), [
            'name' => 'Receptionist Baru',
            'email' => 'receptionist.baru@example.com',
            'phone' => '0812-3456-7890',
            'avatar' => UploadedFile::fake()->image('profile.webp', 400, 400)->size(700),
            'remove_avatar' => 0,
        ]);

        $response->assertRedirect(route('receptionist.profile.edit'))->assertSessionHasNoErrors();
        $receptionist->refresh();

        $this->assertSame('Receptionist Baru', $receptionist->name);
        $this->assertSame('receptionist.baru@example.com', $receptionist->email);
        $this->assertSame('6281234567890', $receptionist->phone);
        $this->assertNotNull($receptionist->avatar_path);
        Storage::disk('public')->assertExists($receptionist->avatar_path);
        Storage::disk('public')->assertMissing('profile-photos/old.jpg');
        $this->actingAs($receptionist)
            ->get(route('receptionist.dashboard'))
            ->assertOk()
            ->assertSee(asset('storage/'.$receptionist->avatar_path));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $receptionist->id,
            'event' => 'update',
            'module' => 'staff_profile',
        ]);
    }

    public function test_owner_can_update_profile_and_remove_existing_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/owner.jpg', 'owner-avatar');
        $owner = User::factory()->owner()->create(['avatar_path' => 'profile-photos/owner.jpg']);

        $this->actingAs($owner)->put(route('owner.profile.update'), [
            'name' => $owner->name,
            'email' => $owner->email,
            'phone' => '0813 0000 1111',
            'remove_avatar' => 1,
        ])->assertRedirect(route('owner.profile.edit'))->assertSessionHasNoErrors();

        $this->assertNull($owner->fresh()->avatar_path);
        $this->assertSame('6281300001111', $owner->fresh()->phone);
        Storage::disk('public')->assertMissing('profile-photos/owner.jpg');
    }

    public function test_staff_must_confirm_current_password_before_changing_it(): void
    {
        $receptionist = User::factory()->receptionist()->create(['password' => 'Password123']);

        $this->actingAs($receptionist)->put(route('receptionist.profile.password.update'), [
            'current_password' => 'PasswordSalah',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->actingAs($receptionist)->put(route('receptionist.profile.password.update'), [
            'current_password' => 'Password123',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ])->assertRedirect(route('receptionist.profile.edit'))->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('PasswordBaru123', $receptionist->fresh()->password));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $receptionist->id,
            'event' => 'password_updated',
            'module' => 'staff_profile',
        ]);
    }

    public function test_staff_cannot_use_another_roles_profile_routes(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $owner = User::factory()->owner()->create();

        $this->actingAs($receptionist)->get(route('owner.profile.edit'))->assertForbidden();
        $this->actingAs($owner)->get(route('receptionist.profile.edit'))->assertForbidden();
    }
}
