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
        Schema::create('optionmenu', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('route')->unique();
            $table->integer('order')->unique();
            $table->string('icon');
            $table->foreignId('groupmenu_id')->constrained('groupmenu');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optionmenu');
    }
};
