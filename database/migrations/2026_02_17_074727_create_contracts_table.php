<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->string('contract_no')->unique();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();

            // ✅ snapshot fields (so contract stays consistent)
            $table->string('room_title')->nullable();
            $table->string('room_type')->nullable();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('monthly_rent', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // draft | active | ended | cancelled
            $table->string('status')->default('draft');

            // unpaid | payment_submitted | paid | refunded
            $table->string('payment_status')->default('unpaid');

            // saved pdf path like: contracts/contract-SR-CTR-2026-00001.pdf
            $table->string('pdf_path')->nullable();

            $table->timestamp('signed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
