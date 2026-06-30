<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'capacity')) {
                $table->unsignedTinyInteger('capacity')->default(1)->after('room_type');
            }

            if (!Schema::hasColumn('rooms', 'available_slots')) {
                $table->unsignedTinyInteger('available_slots')->default(1)->after('capacity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'available_slots')) {
                $table->dropColumn('available_slots');
            }
            if (Schema::hasColumn('rooms', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};
