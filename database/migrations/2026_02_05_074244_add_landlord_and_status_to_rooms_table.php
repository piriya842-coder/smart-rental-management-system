<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            // owner of the room (landlord user id)
            if (!Schema::hasColumn('rooms', 'landlord_id')) {
                $table->foreignId('landlord_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // listing workflow
            // draft = not visible to students
            // active = visible to students
            // inactive = hidden from students
            if (!Schema::hasColumn('rooms', 'status')) {
                $table->string('status')->default('draft')->after('landlord_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            if (Schema::hasColumn('rooms', 'landlord_id')) {
                $table->dropForeign(['landlord_id']);
                $table->dropColumn('landlord_id');
            }

            if (Schema::hasColumn('rooms', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
