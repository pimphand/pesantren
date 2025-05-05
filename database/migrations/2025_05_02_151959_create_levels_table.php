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
        Schema::create('levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->renameColumn('level', 'level_id');
        });
    

        // Tambahkan foreign key constraint
        Schema::table('students', function (Blueprint $table) {
            $table->uuid('level_id')->nullable()->change(); // make nullable just in case
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
