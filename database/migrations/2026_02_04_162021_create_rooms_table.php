<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            // landlord user
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();

            // main info
            $table->string('title');
            $table->text('description')->nullable();

            // location + pricing
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();

            $table->decimal('price_monthly', 10, 2);
            $table->decimal('distance_km', 6, 2)->nullable(); // distance to MSU (manual input for now)

            // room details
            $table->enum('room_type', ['single', 'shared', 'studio'])->default('single');
            $table->enum('gender_preference', ['any', 'male', 'female'])->default('any');

            // facilities as JSON (easy for filtering later)
            $table->json('facilities')->nullable(); // e.g. ["wifi","parking","aircond"]
            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
