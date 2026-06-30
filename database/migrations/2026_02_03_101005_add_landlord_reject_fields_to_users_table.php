<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // if you already have landlord_status, keep it
            if (!Schema::hasColumn('users', 'landlord_status')) {
                $table->string('landlord_status')->nullable()->after('role'); 
                // values: pending, approved, rejected
            }

            if (!Schema::hasColumn('users', 'landlord_verified_at')) {
                $table->timestamp('landlord_verified_at')->nullable()->after('landlord_status');
            }

            if (!Schema::hasColumn('users', 'landlord_rejected_at')) {
                $table->timestamp('landlord_rejected_at')->nullable()->after('landlord_verified_at');
            }

            if (!Schema::hasColumn('users', 'landlord_rejection_reason')) {
                $table->string('landlord_rejection_reason')->nullable()->after('landlord_rejected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'landlord_rejection_reason')) $table->dropColumn('landlord_rejection_reason');
            if (Schema::hasColumn('users', 'landlord_rejected_at')) $table->dropColumn('landlord_rejected_at');
            if (Schema::hasColumn('users', 'landlord_verified_at')) $table->dropColumn('landlord_verified_at');
            // landlord_status maybe used already—up to you to drop or keep
        });
    }
};
