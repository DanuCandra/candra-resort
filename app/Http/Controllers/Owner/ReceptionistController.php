<?php

namespace App\Http\Controllers\Owner;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\ReceptionistRequest;
use App\Http\Requests\Owner\ResetReceptionistPasswordRequest;
use App\Models\Stay;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceptionistController extends Controller
{
    public function index(Request $request): View
    {
        $receptionists = User::query()->where('role', UserRole::Receptionist->value)
            ->withCount(['receivedPayments', 'auditLogs'])
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('email', 'like', '%'.$request->string('search').'%')
                ->orWhere('employee_code', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('active'), fn ($query) => $query->where('is_active', $request->boolean('active')))
            ->latest()->paginate(12)->withQueryString();

        return view('owner.receptionists.index', compact('receptionists'));
    }

    public function create(): View
    {
        return view('owner.receptionists.create');
    }

    public function store(ReceptionistRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = UserRole::Receptionist;
        $data['created_by'] = $request->user()->id;
        $receptionist = User::create($data);
        AuditLogger::record($request, 'create', 'receptionists', $receptionist, 'Owner membuat akun Receptionist '.$receptionist->name.'.', null, $receptionist->toArray());

        return redirect()->route('owner.receptionists.show', $receptionist)->with('success', 'Akun Receptionist berhasil dibuat.');
    }

    public function show(User $receptionist): View
    {
        $this->ensureReceptionist($receptionist);
        $receptionist->load(['creator', 'auditLogs' => fn ($query) => $query->latest()->limit(15)])
            ->loadCount(['receivedPayments', 'auditLogs']);

        return view('owner.receptionists.show', [
            'receptionist' => $receptionist,
            'checkedInCount' => Stay::query()->where('checked_in_by', $receptionist->id)->count(),
            'checkedOutCount' => Stay::query()->where('checked_out_by', $receptionist->id)->count(),
        ]);
    }

    public function edit(User $receptionist): View
    {
        $this->ensureReceptionist($receptionist);

        return view('owner.receptionists.edit', compact('receptionist'));
    }

    public function update(ReceptionistRequest $request, User $receptionist): RedirectResponse
    {
        $this->ensureReceptionist($receptionist);
        $oldValues = $receptionist->toArray();
        $data = $request->validated();
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        unset($data['role'], $data['created_by']);
        $receptionist->update($data);
        if (! $receptionist->is_active) {
            $this->invalidateSessions($receptionist);
        }
        AuditLogger::record($request, 'update', 'receptionists', $receptionist, 'Owner memperbarui akun Receptionist '.$receptionist->name.'.', $oldValues, $receptionist->fresh()->toArray());

        return redirect()->route('owner.receptionists.show', $receptionist)->with('success', 'Data Receptionist berhasil diperbarui.');
    }

    public function toggle(Request $request, User $receptionist): RedirectResponse
    {
        $this->ensureReceptionist($receptionist);
        $receptionist->update(['is_active' => ! $receptionist->is_active]);
        if (! $receptionist->is_active) {
            $this->invalidateSessions($receptionist);
        }
        AuditLogger::record($request, $receptionist->is_active ? 'activate' : 'deactivate', 'receptionists', $receptionist, 'Owner '.($receptionist->is_active ? 'mengaktifkan' : 'menonaktifkan').' Receptionist '.$receptionist->name.'.');

        return back()->with('success', 'Status Receptionist berhasil diubah.');
    }

    public function resetPassword(ResetReceptionistPasswordRequest $request, User $receptionist): RedirectResponse
    {
        $this->ensureReceptionist($receptionist);
        $receptionist->update(['password' => $request->validated('password')]);
        $this->invalidateSessions($receptionist);
        AuditLogger::record($request, 'reset_password', 'receptionists', $receptionist, 'Owner mereset password Receptionist '.$receptionist->name.'.');

        return back()->with('success', 'Password Receptionist berhasil direset.');
    }

    public function destroy(Request $request, User $receptionist): RedirectResponse
    {
        $this->ensureReceptionist($receptionist);
        $hasHistory = $receptionist->receivedPayments()->exists()
            || $receptionist->auditLogs()->exists()
            || Stay::query()->where('checked_in_by', $receptionist->id)->orWhere('checked_out_by', $receptionist->id)->exists();

        if ($hasHistory) {
            $receptionist->update(['is_active' => false]);
            AuditLogger::record($request, 'deactivate', 'receptionists', $receptionist, 'Receptionist dinonaktifkan karena memiliki histori operasional.');

            return back()->with('success', 'Akun memiliki histori sehingga dinonaktifkan, bukan dihapus.');
        }

        $receptionist->forceFill(['is_active' => false])->save();
        AuditLogger::record($request, 'delete', 'receptionists', $receptionist, 'Owner menghapus akun Receptionist '.$receptionist->name.'.');
        $receptionist->delete();

        return redirect()->route('owner.receptionists.index')->with('success', 'Akun Receptionist berhasil dihapus.');
    }

    private function ensureReceptionist(User $receptionist): void
    {
        abort_unless($receptionist->role === UserRole::Receptionist, 404);
    }

    private function invalidateSessions(User $receptionist): void
    {
        DB::table('sessions')->where('user_id', $receptionist->id)->delete();
    }
}
