<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cash, Debit, QRIS, Bank Transfer, Midtrans, dll.
            $table->string('code', 100)->unique();
            $table->string('type', 50)->index();
            // cash|debit|qris|bank_transfer|card|ewallet|virtual_account|gateway|other
            $table->string('channel', 30)->default('manual')->index(); // manual|midtrans
            $table->string('gateway_method_code')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code', 100)->unique();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stay_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('folio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('purpose', 40)->default('reservation')->index();
            // reservation|deposit|folio|refund|other
            $table->string('status', 30)->default('pending')->index();
            // pending|paid|failed|refunded|cancelled|expired
            $table->string('source', 30)->default('guest')->index();
            // guest|receptionist|midtrans
            $table->char('currency', 3)->default('IDR');
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable()->index();
            $table->string('proof_path')->nullable();

            // Data transaksi Midtrans.
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable()->unique();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_transaction_status')->nullable()->index();
            $table->string('midtrans_fraud_status')->nullable();
            $table->string('midtrans_bank')->nullable();
            $table->string('midtrans_va_number')->nullable();
            $table->timestamp('midtrans_expiry_time')->nullable();
            $table->json('midtrans_response')->nullable();

            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('refunded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'status']);
            $table->index(['folio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
    }
};
