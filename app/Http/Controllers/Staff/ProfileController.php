<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UpdateProfilePasswordRequest;
use App\Http\Requests\Staff\UpdateProfileRequest;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Mengelola profil dan password staf.
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('staff.profile.edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldValues = $user->only(['name', 'email', 'phone', 'avatar_path']);
        $data = $request->safe()->only(['name', 'email', 'phone']);
        $oldAvatar = $user->avatar_path;

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('profile-photos/'.$user->id, 'public');
        } elseif ($request->validated('remove_avatar')) {
            $data['avatar_path'] = null;
        }

        $user->update($data);

        if ($oldAvatar && $oldAvatar !== $user->avatar_path) {
            Storage::disk('public')->delete($oldAvatar);
        }

        AuditLogger::record(
            $request,
            'update',
            'staff_profile',
            $user,
            $user->role->label().' memperbarui profil pribadi.',
            $oldValues,
            $user->fresh()->only(['name', 'email', 'phone', 'avatar_path'])
        );

        return redirect()->route($user->profileRouteName())->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['password' => $request->validated('password')]);

        AuditLogger::record(
            $request,
            'password_updated',
            'staff_profile',
            $user,
            $user->role->label().' mengganti password akun pribadi.'
        );

        return redirect()->route($user->profileRouteName())->with('success', 'Password berhasil diganti.');
    }
}
