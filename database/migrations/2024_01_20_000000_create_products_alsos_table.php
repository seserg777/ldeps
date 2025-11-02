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
        Schema::create('vjprf_jshopping_products_alsos', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('product_also_id')->unsigned();
            
            // Add indexes for better performance
            $table->index('product_id');
            $table->index('product_also_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vjprf_jshopping_products_alsos');
    }
};

