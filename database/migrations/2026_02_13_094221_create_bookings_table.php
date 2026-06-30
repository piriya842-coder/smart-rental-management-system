<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // ✅ MUST in your system
            $table->date('contract_start_date');
            $table->date('contract_end_date');

            // money
            $table->unsignedInteger('deposit_amount')->default(100); // RM
            $table->decimal('monthly_rent', 10, 2)->default(0);
            $table->decimal('total_due', 10, 2)->default(0);

            // status flow
            // pending = booking created, waiting payment
            // payment_submitted = receipt uploaded
            // paid = verified/accepted (you can keep as paid for now)
            // cancelled / rejected / expired
            $table->string('status')->default('pending');

            // optional notes
            $table->text('student_note')->nullable();

            $table->timestamps();

            $table->index(['room_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['landlord_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
