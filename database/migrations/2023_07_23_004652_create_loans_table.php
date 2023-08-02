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
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_request_id')->nullable();
            $table->uuid('user_id')->nullable(false);
            $table->float('amount', 8, 2);
            $table->float('interest', 8, 2)->nullable(true);
            $table->float('amount_paid', 8, 2)->default(0);
            $table->float('interest_paid', 8, 2)->default(0);
            $table->float('interest_rate', 8, 2);
            $table->integer('duration');
            $table->date('start_date');
            $table->uuid('admin_id');
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('user_respond')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
