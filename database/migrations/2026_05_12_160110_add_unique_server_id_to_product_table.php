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
        //Identificar qué server_id están duplicados
        $dups = \Illuminate\Support\Facades\DB::table('product')
            ->select('server_id')
            ->whereNotNull('server_id')
            ->groupBy('server_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('server_id');

        if ($dups->isNotEmpty()) {
            foreach ($dups as $dupServerId) {
                $ids = \Illuminate\Support\Facades\DB::table('product')
                    ->where('server_id', $dupServerId)
                    ->orderBy('id')
                    ->pluck('id');

                $idsToIgnore = $ids->slice(1)->values();

                if ($idsToIgnore->isNotEmpty()) {
                    \Illuminate\Support\Facades\DB::table('product')
                        ->whereIn('id', $idsToIgnore)
                        ->update(['server_id' => null]);
                }
            }
        }

        Schema::table('product', function (Blueprint $table) {
            $table->unique('server_id', 'product_server_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropUnique('product_server_id_unique');
        });
    }
};
