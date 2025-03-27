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
        Schema::create('banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->integer('account_number');
            $table->string('account_name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('bank_id')->nullable();
            $table->string('payment_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
    }
};
