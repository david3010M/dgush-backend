<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('detailweb', 1000);
            $table->decimal('price1');
            $table->decimal('price2');
            $table->decimal('price12')->nullable();
            $table->decimal('priceOferta')->nullable();
            $table->decimal('priceLiquidacion')->nullable();
            $table->decimal('percentageDiscount')->nullable();
            $table->decimal('score', 8, 1)->default(0);
//            $table->string('condition')->values(['new', 'used'])->default('new');
            $table->string('status')->values(['onsale', 'new', ''])->default('');
            $table->boolean('liquidacion')->default(false);
            $table->foreignId('subcategory_id')->constrained('subcategory');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
