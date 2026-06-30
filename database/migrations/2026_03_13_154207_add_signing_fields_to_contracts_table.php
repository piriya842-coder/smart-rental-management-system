<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'agreed_to_terms')) {
                $table->boolean('agreed_to_terms')->default(false)->after('payment_status');
            }

            if (!Schema::hasColumn('contracts', 'signed_name')) {
                $table->string('signed_name')->nullable()->after('agreed_to_terms');
            }

            if (!Schema::hasColumn('contracts', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signed_name');
            }

            if (!Schema::hasColumn('contracts', 'signature_ip')) {
                $table->string('signature_ip')->nullable()->after('signed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'agreed_to_terms')) {
                $table->dropColumn('agreed_to_terms');
            }

            if (Schema::hasColumn('contracts', 'signed_name')) {
                $table->dropColumn('signed_name');
            }

            if (Schema::hasColumn('contracts', 'signed_at')) {
                $table->dropColumn('signed_at');
            }

            if (Schema::hasColumn('contracts', 'signature_ip')) {
                $table->dropColumn('signature_ip');
            }
        });
    }
};