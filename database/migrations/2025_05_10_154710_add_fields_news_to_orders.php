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
        Schema::table('order', function (Blueprint $table) {
            $table->text('first_token')->nullable();
            $table->text('second_token')->nullable();
            $table->string('stage')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('server_id')->nullable();
            //faltan más

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['first_token','second_token','stage','bill_number','server_id']);
        });
    }
};
