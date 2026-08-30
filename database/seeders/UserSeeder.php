<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $ownerPassword = env('OWNER_PASSWORD') ?: 'Password123';
        $receptionistPassword = env('RECEPTIONIST_PASSWORD') ?: 'Password123';

        if (app()->isProduction() && (blank($ownerPassword) || blank($receptionistPassword))) {
            throw new \RuntimeException('OWNER_PASSWORD dan RECEPTIONIST_PASSWORD wajib diatur sebelum menjalankan seeder di production.');
        }

        $owner = $this->claimAccount(
            env('OWNER_EMAIL', 'danucahndx33@gmail.com'),
            'OWN-001',
            'owner',
            [
                'name' => env('OWNER_NAME', 'Owner Candra Resort'),
                'phone' => '6281234567800',
                'role' => UserRole::Owner,
                'password' => $ownerPassword,
                'is_active' => true,
                'created_by' => null,
            ]
        );

        $this->claimAccount(
            env('RECEPTIONIST_EMAIL', 'danucandraa100@gmail.com'),
            'REC-001',
            'receptionist',
            [
                'name' => 'Receptionist Candra Resort',
                'phone' => '6281234567801',
                'role' => UserRole::Receptionist,
                'password' => $receptionistPassword,
                'is_active' => true,
                'created_by' => $owner->id,
            ]
        );
    }

    /** @param array<string, mixed> $attributes */
    private function claimAccount(string $email, string $employeeCode, string $username, array $attributes): User
    {
        $account = User::query()->withTrashed()->where('email', $email)->first()
            ?? User::query()->withTrashed()->where('employee_code', $employeeCode)->first()
            ?? new User;

        $this->releaseIdentifier('employee_code', $employeeCode, $account);
        $this->releaseIdentifier('username', $username, $account);

        $account->forceFill([
            ...$attributes,
            'email' => $email,
            'employee_code' => $employeeCode,
            'username' => $username,
            'deleted_at' => null,
        ])->save();

        return $account->refresh();
    }

    private function releaseIdentifier(string $column, string $value, User $target): void
    {
        $conflict = User::query()->withTrashed()
            ->where($column, $value)
            ->when($target->exists, fn ($query) => $query->where($target->getKeyName(), '!=', $target->getKey()))
            ->first();

        if (! $conflict) {
            return;
        }

        $legacyValue = $column === 'username'
            ? "legacy_{$conflict->id}_{$value}"
            : "LEGACY-{$conflict->id}";

        $conflict->forceFill([$column => $legacyValue, 'is_active' => false])->save();
    }
}
