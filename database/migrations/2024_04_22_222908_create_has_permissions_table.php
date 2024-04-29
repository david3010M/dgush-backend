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
        Schema::create('has_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('typeuser_id')->constrained('typeuser');
            $table->foreignId('permission_id')->constrained('permission');
//            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('has_permission');
    }
};
