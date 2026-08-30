<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();

            // Snapshot saat check-in. Nomor ini digunakan untuk verifikasi QR kamar.
            $table->string('guest_name');
            $table->string('guest_phone', 30)->index();

            $table->string('identity_type', 50)->nullable(); // KTP|SIM|Passport|Other
            $table->string('identity_number', 100)->nullable();
            $table->string('identity_photo_path')->nullable(); // private storage

            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('check_in_at')->nullable()->index();
            $table->timestamp('check_out_at')->nullable()->index();

            $table->string('key_code', 100)->nullable();
            $table->timestamp('key_issued_at')->nullable();
            $table->timestamp('key_returned_at')->nullable();

            $table->decimal('security_deposit_amount', 15, 2)->default(0);
            $table->string('status', 30)->default('active')->index(); // active|completed|cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_room_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('session_token', 128)->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['stay_id', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_room_accesses');
        Schema::dropIfExists('stays');
    }
};
