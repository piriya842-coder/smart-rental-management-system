<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Student requests cancel/refund AFTER payment submitted/paid
            $table->timestamp('cancel_requested_at')->nullable()->after('student_note');
            $table->text('cancel_request_reason')->nullable()->after('cancel_requested_at');

            // Landlord/admin may approve later
            $table->timestamp('cancelled_at')->nullable()->after('cancel_request_reason');
            $table->text('cancelled_reason')->nullable()->after('cancelled_at');

            // Refund tracking (future)
            $table->timestamp('refunded_at')->nullable()->after('cancelled_reason');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancel_requested_at',
                'cancel_request_reason',
                'cancelled_at',
                'cancelled_reason',
                'refunded_at',
            ]);
        });
    }
};
