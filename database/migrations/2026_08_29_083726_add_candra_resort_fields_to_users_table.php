<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel users bawaan Laravel tetap digunakan.
        // Migration ini hanya menambahkan field yang dibutuhkan Candra Resort.
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->string('phone', 30)->nullable()->index();
            $table->string('employee_code', 50)->nullable()->unique();
            $table->string('role', 30)->default('guest')->index(); // guest|receptionist|owner
            $table->string('avatar_path')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'username',
                'phone',
                'employee_code',
                'role',
                'avatar_path',
                'date_of_birth',
                'gender',
                'address',
                'is_active',
                'last_login_at',
                'created_by',
            ]);
        });
    }
};
