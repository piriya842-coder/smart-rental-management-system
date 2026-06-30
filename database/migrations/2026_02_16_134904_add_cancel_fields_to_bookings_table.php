<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        if (!Schema::hasColumn('bookings', 'cancelled_at')) {
            $table->timestamp('cancelled_at')->nullable()->after('student_note');
        }

        if (!Schema::hasColumn('bookings', 'cancelled_reason')) {
            $table->string('cancelled_reason', 500)->nullable()->after('cancelled_at');
        }
    });
}

    public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        if (Schema::hasColumn('bookings', 'cancelled_reason')) {
            $table->dropColumn('cancelled_reason');
        }

        if (Schema::hasColumn('bookings', 'cancelled_at')) {
            $table->dropColumn('cancelled_at');
        }
    });
}

};
