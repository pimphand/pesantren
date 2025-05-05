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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_type')->comment('Top up, Transaction, etc');
            $table->foreignId('user_id')->constrained();
            $table->foreignUuid('order_id')->nullable()->constrained();
            $table->string('idempotency_key')->unique()->nullable();
            $table->uuid('to_user_id')->nullable()->comment('User receiving payment, if applicable');
            $table->bigInteger('amount')->default(0);
            $table->string('status')->default('pending')->comment('paid, expired, canceled, failed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('note')->nullable();
            $table->string('proof_of_payment')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
