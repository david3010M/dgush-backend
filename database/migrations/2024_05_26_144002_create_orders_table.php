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
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal', 10);
            $table->decimal('total', 10);
            $table->decimal('quantity', 10);
            $table->timestamp('date');
            $table->foreignId('user_id')->constrained('user');
            $table->foreignId('coupon_id')->constrained('coupon')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};