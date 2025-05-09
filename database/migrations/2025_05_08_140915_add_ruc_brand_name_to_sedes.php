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
        Schema::table('sedes', function (Blueprint $table) {
            $table->string('ruc')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('server_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sedes', function (Blueprint $table) {
            $table->dropColumn(['ruc', 'brand_name','server_id']);
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
        });
    }
};
