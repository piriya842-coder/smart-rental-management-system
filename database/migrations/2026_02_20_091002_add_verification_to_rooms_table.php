<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // ✅ Soft verification: default is APPROVED so existing system won't break
            if (!Schema::hasColumn('rooms', 'verification_status')) {
                $table->string('verification_status')->default('approved')->after('updated_at');
            }

            if (!Schema::hasColumn('rooms', 'verification_reason')) {
                $table->text('verification_reason')->nullable()->after('verification_status');
            }

            if (!Schema::hasColumn('rooms', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('rooms', 'verification_reason')) {
                $table->dropColumn('verification_reason');
            }
            if (Schema::hasColumn('rooms', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });
    }
};