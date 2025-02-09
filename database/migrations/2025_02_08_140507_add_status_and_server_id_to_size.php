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
        Schema::table('size', function (Blueprint $table) {
            $table->dropUnique(['value']); // Elimina la restricción UNIQUE de 'value'
            $table->string('status')->nullable();
            $table->string('server_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('size', function (Blueprint $table) {
            
            $table->dropColumn(['status', 'server_id']);
            $table->unique('value');
        });
    }
};
