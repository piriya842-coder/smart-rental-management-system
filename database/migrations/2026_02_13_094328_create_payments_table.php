<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('method')->default('qr'); // qr | fpx (future)
            $table->string('status')->default('pending'); // pending | submitted | paid | failed

            // for QR proof
            $table->string('receipt_path')->nullable();

            // for future payment gateway
            $table->string('provider')->nullable();
            $table->string('provider_ref')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
