<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('student_profiles', function (Blueprint $table) {
        $table->string('nric_passport', 50)->nullable()->after('date_of_birth');
    });
}

public function down(): void
{
    Schema::table('student_profiles', function (Blueprint $table) {
        $table->dropColumn('nric_passport');
    });
}
};
