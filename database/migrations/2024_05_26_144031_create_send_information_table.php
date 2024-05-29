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
        Schema::create('send_information', function (Blueprint $table) {
            $table->id();
            $table->string('names');
            $table->string('dni');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('reference');
            $table->string('comment');
            $table->string('method');
            $table->foreignId('province_id')->constrained('province');
            $table->foreignId('order_id')->constrained('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_information');
    }
};
