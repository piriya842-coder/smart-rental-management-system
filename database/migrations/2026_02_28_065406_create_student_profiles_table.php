<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();

            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 80)->nullable();
            $table->string('race', 80)->nullable();
            $table->string('religion', 80)->nullable();

            $table->string('country', 100)->nullable();

            // Address (optional - student can update)
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();

            // Emergency contact
            $table->string('emergency_name', 120)->nullable();
            $table->string('emergency_phone', 30)->nullable();
            $table->string('emergency_relationship', 60)->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};