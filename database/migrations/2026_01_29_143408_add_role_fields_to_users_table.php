<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Role
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('email');
            }

            // Common fields
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('role');
            }

            // Student fields (optional, can be null for other roles)
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id', 30)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('student_id');
            }

            // Landlord fields (optional)
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('company_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop only if exists (safe rollback)
            $columns = [
                'role',
                'phone',
                'student_id',
                'gender',
                'company_name',
                'address',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
