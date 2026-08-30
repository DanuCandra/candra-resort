<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 100)->unique();
            $table->foreignId('guest_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();

            $table->string('source', 30)->default('online')->index(); // online|walk_in

            // Snapshot data guest saat reservasi, termasuk walk-in tanpa akun.
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 30)->index();

            $table->date('check_in_date')->index();
            $table->date('check_out_date')->index();
            $table->unsignedInteger('total_nights')->default(1);
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->time('estimated_arrival_time')->nullable();

            $table->string('status', 40)->default('pending_payment')->index();
            // pending_payment|paid|confirmed|checked_in|checked_out|cancelled|no_show
            $table->string('payment_status', 30)->default('unpaid')->index();
            // unpaid|partial|paid|refunded

            $table->char('currency', 3)->default('IDR');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('service_charge_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('promo_code_snapshot', 100)->nullable();

            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('payment_due_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(
                ['room_type_id', 'check_in_date', 'check_out_date'],
                'reservation_roomtype_dates_idx'
            );
            $table->index(['status', 'check_in_date']);
            $table->index(['status', 'check_out_date']);
        });

        Schema::create('reservation_nights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_rate_id')->nullable()->constrained('room_rates')->nullOnDelete();
            $table->date('stay_date');
            $table->string('rate_name')->nullable();
            $table->decimal('price_before_discount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('net_price', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['reservation_id', 'stay_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_nights');
        Schema::dropIfExists('reservations');
    }
};
