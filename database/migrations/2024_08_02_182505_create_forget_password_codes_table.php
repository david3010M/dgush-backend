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
        Schema::create('forget_password_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('code');
            $table->boolean('used')->default(false);
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forget_password_codes');
    }
};
