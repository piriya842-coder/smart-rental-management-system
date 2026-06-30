<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('room_images');
    }

    public function down(): void
    {
        Schema::create('room_images', function ($table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->string('path');
            $table->boolean('is_cover')->default(false);
            $table->timestamps();
        });
    }
};
