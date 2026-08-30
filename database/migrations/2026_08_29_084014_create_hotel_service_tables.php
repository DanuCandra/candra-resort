<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name');
            $table->string('category', 100)->nullable()->index();
            // massage|spa|laundry|transport|extra_bed|etc
            $table->longText('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('price_unit', 50)->default('per_order');
            // per_order|per_hour|per_item|per_kg|etc
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('requires_schedule')->default(false);
            $table->boolean('is_available')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 100)->unique();
            $table->foreignId('stay_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('hotel_service_id')->constrained()->restrictOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 30)->default('qr')->index(); // qr|receptionist
            $table->string('status', 30)->default('requested')->index();
            // requested|accepted|scheduled|processing|completed|cancelled
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->boolean('charge_to_room')->default(true);
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
        Schema::dropIfExists('hotel_services');
    }
};
