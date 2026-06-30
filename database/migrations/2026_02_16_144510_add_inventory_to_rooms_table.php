<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            // single / shared
            if (!Schema::hasColumn('rooms', 'room_type')) {
                $table->string('room_type')->default('single')->after('id'); // single/shared
            }

            // shared room capacity
            if (!Schema::hasColumn('rooms', 'total_slots')) {
                $table->unsignedInteger('total_slots')->default(1)->after('room_type');
            }

            if (!Schema::hasColumn('rooms', 'available_slots')) {
                $table->unsignedInteger('available_slots')->default(1)->after('total_slots');
            }

            // single room lock
            if (!Schema::hasColumn('rooms', 'is_reserved')) {
                $table->boolean('is_reserved')->default(false)->after('available_slots');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            if (Schema::hasColumn('rooms', 'is_reserved')) {
                $table->dropColumn('is_reserved');
            }
            if (Schema::hasColumn('rooms', 'available_slots')) {
                $table->dropColumn('available_slots');
            }
            if (Schema::hasColumn('rooms', 'total_slots')) {
                $table->dropColumn('total_slots');
            }
            if (Schema::hasColumn('rooms', 'room_type')) {
                $table->dropColumn('room_type');
            }
        });
    }
};
