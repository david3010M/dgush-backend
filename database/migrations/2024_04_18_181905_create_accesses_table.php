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
        Schema::create('access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('optionmenu_id')->constrained('optionmenu');
            $table->foreignId('typeuser_id')->constrained('typeuser');
//            $table->softDeletes();
            $table->timestamps();
            $table->unique(['optionmenu_id', 'typeuser_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access');
    }
};
