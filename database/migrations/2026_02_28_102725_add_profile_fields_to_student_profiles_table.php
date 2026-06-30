<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {

            if (!Schema::hasColumn('student_profiles', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->unique('user_id');
            }

            if (!Schema::hasColumn('student_profiles', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'nationality')) {
                $table->string('nationality', 80)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'race')) {
                $table->string('race', 80)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'religion')) {
                $table->string('religion', 80)->nullable();
            }

            if (!Schema::hasColumn('student_profiles', 'phone')) {
                $table->string('phone', 30)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'gender')) {
                $table->string('gender', 20)->nullable();
            }

            if (!Schema::hasColumn('student_profiles', 'address_line1')) {
                $table->string('address_line1', 255)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'address_line2')) {
                $table->string('address_line2', 255)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'postcode')) {
                $table->string('postcode', 20)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'city')) {
                $table->string('city', 100)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'state')) {
                $table->string('state', 100)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'country')) {
                $table->string('country', 100)->nullable();
            }

            if (!Schema::hasColumn('student_profiles', 'emergency_name')) {
                $table->string('emergency_name', 120)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'emergency_phone')) {
                $table->string('emergency_phone', 30)->nullable();
            }
            if (!Schema::hasColumn('student_profiles', 'emergency_relationship')) {
                $table->string('emergency_relationship', 60)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // (Optional) keep empty to avoid breaking existing data
        });
    }
};