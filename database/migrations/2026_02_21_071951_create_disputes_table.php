<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            // Ticket code like D-000012
            $table->string('code')->unique();

            // Category of dispute
            $table->string('category')->default('general'); // payment, booking, listing, behaviour, other

            // Priority
            $table->string('priority')->default('medium'); // high, medium, low

            // Status
            $table->string('status')->default('open'); // open, in_review, resolved, rejected

            // Who submitted (student usually, but keep flexible)
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();

            // Student/Landlord involved (optional but recommended)
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('landlord_id')->nullable()->constrained('users')->nullOnDelete();

            // Related booking/room (optional)
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();

            // Ticket content
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Evidence upload
            $table->string('evidence_path')->nullable();

            // Admin notes + outcome
            $table->text('admin_note')->nullable();
            $table->string('resolution')->nullable(); // refund_approved, refund_rejected, warning, cancel_booking, flag_listing, other
            $table->text('outcome_details')->nullable();

            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'priority', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};