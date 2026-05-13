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
        // Desactiva y hace soft-delete a los productos huérfanos que quedaron de la migración anterior
        \Illuminate\Support\Facades\DB::table('product')->whereNull('server_id')->update([
            'status_server' => 0,
            'deleted_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
