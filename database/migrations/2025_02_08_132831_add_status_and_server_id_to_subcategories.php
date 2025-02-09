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
        Schema::table('subcategory', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
            $table->string('status')->nullable();
            $table->string('server_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategory', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
            $table->dropColumn(['status', 'server_id']);
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }
};
