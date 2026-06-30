<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // landlord verification
            $table->string('landlord_status')->default('pending')->after('role'); // pending|approved|rejected
            $table->timestamp('landlord_verified_at')->nullable()->after('landlord_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['landlord_status', 'landlord_verified_at']);
        });
    }
};
