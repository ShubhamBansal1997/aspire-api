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
        Schema::create('repayments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->integer('payment_no');
            $table->float('repayable_amount', 8, 2);
            $table->float('amount', 8, 2);
            $table->float('interest', 8, 2);
            $table->float('repayable_amount_paid', 8, 2)->nullable();
            $table->float('amount_paid', 8, 2)->nullable();
            $table->float('interest_paid', 8, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->date('due_date');
            $table->float('weekly_interest_rate', 8, 2);
            $table->tinyInteger('is_paid')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
