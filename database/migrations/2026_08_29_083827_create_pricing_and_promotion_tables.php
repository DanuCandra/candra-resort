<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Weekend, High Season, Holiday, Special Rate
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->json('days_of_week')->nullable(); // [1,2,3,4,5,6,7]
            $table->decimal('price_per_night', 15, 2);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type', 20)->default('percent'); // percent|fixed
            $table->decimal('discount_value', 15, 2);
            $table->decimal('max_discount_amount', 15, 2)->nullable();
            $table->decimal('minimum_transaction', 15, 2)->default(0);
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->unsignedInteger('usage_quota')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('max_usage_per_guest')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promotion_room_type', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['promotion_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_room_type');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('room_rates');
    }
};
