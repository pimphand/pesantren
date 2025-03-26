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
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('subject_id');
            $table->string('subject_id')->after('subject_type')->nullable();
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->boolean('is_tax')->default(false);
            $table->integer('tax')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('subject_id');
            $table->bigInteger('subject_id')->nullable();
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('is_tax');
            $table->dropColumn('tax');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
};
