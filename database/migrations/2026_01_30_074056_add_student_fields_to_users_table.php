<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // If you already have role column, do NOT add again
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('email');
            }

            // Student info
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'programme')) {
                $table->string('programme')->nullable()->after('student_id');
            }

            // Personal info
            if (!Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('programme');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('age'); // Male/Female/Other
            }

            // Contact + Address
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'address_line1')) {
                $table->string('address_line1')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'address_line2')) {
                $table->string('address_line2')->nullable()->after('address_line1');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address_line2');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'postcode')) {
                $table->string('postcode')->nullable()->after('state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'student_id','programme','age','gender','phone',
                'address_line1','address_line2','city','state','postcode'
            ];

            foreach ($cols as $c) {
                if (Schema::hasColumn('users', $c)) $table->dropColumn($c);
            }
        });
    }
};
