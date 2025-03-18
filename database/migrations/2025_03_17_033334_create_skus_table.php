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
        Schema::create('skus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('product_id')->constrained();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('photo')->nullable();
            $table->bigInteger('price')->default(0);
            $table->integer('stock')->default(0);
            $table->json('attributes')->nullable()->comment('SKU attributes : color, size, etc');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
