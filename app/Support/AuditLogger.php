<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

// Mencatat aktivitas penting tanpa data sensitif.
class AuditLogger
{
    public static function record(
        Request $request,
        string $event,
        string $module,
        ?Model $model,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        AuditLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'module' => $module,
            'auditable_type' => $model?->getMorphClass(),
            'auditable_id' => $model?->getKey(),
            'description' => $description,
            'old_values' => self::withoutSensitiveValues($oldValues),
            'new_values' => self::withoutSensitiveValues($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private static function withoutSensitiveValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return collect($values)->except([
            'password', 'remember_token', 'qr_token', 'identity_number',
            'midtrans_response', 'midtrans_transaction_id',
        ])->all();
    }
}
