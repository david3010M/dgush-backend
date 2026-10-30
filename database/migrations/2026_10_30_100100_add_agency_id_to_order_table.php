<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Agencia elegida en un pedido modo ENVIO (360Sys la devuelve como agency_id). */
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->after('district_id')->constrained('agencies');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
        });
    }
};
