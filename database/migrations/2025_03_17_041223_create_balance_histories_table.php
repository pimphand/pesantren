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
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained();
            $table->bigInteger('balance')->default(0)->comment('total money in your account');
            $table->bigInteger('amount')->default(0)->comment('total amount of money');
            $table->bigInteger('debit')->default(0)->comment('Money going out of your account');
            $table->bigInteger('credit')->default(0)->comment('Money coming into your account');
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id');
            $table->string('type')->default('TOP UP')->comment('top up, transaction, withdraw');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_histories');
    }
};
