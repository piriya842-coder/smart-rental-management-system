<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // make landlord_status nullable, no default "pending" for everyone
            if (Schema::hasColumn('users', 'landlord_status')) {
                $table->string('landlord_status')->nullable()->change();
            }

            if (Schema::hasColumn('users', 'landlord_verified_at')) {
                $table->timestamp('landlord_verified_at')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // optional: keep empty or revert if you want
    }
};
