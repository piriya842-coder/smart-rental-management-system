<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            // ✅ Make these safe (NOT enum) to avoid "Data truncated"
            $table->string('room_type')->nullable()->change();
            $table->string('gender_preference')->nullable()->change();

            // ✅ Use ONE image field (cover image)
            if (!Schema::hasColumn('rooms', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('is_available');
            }

            // If you have both image_path + cover_image, keep only cover_image.
            // You can remove image_path later if you want (optional).
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // rollback (keep it simple)
            if (Schema::hasColumn('rooms', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });
    }
};
