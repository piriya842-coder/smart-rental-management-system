<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_rents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();

            $table->string('month_label'); // March 2026
            $table->date('month_date');    // 2026-03-01
            $table->date('due_date');

            $table->decimal('amount', 10, 2);

            $table->string('status')->default('upcoming'); 
            // upcoming, due_soon, overdue, submitted, paid

            $table->string('receipt_path')->nullable();
            $table->string('method')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_rents');
    }
};