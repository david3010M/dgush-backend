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
            $table->string('number')->nullable();
            $table->decimal('subtotal', 10);
            $table->decimal('discount', 10);
            $table->decimal('sendCost', 10);
            $table->decimal('total', 10);
            $table->decimal('quantity', 10);
            $table->timestamp('date');
            $table->date('deliveryDate')->nullable();
            $table->date('shippingDate')->nullable();
            $table->string('status')->default('VERIFICANDO');
            $table->string('description')->nullable();
            $table->string('numberPayment')->nullable();
            $table->string('paymentId')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('coupon_id')->nullable()->constrained('coupon');
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
