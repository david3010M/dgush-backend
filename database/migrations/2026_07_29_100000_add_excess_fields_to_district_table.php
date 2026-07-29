<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Costo de exceso en envío interdepartamental (modo ENVIO).
     *
     * excess       => monto adicional (S/) que se suma al costo de envío.
     * excessFactor => cada cuántas prendas se vuelve a aplicar ese monto.
     *
     * Ambos llegan desde 360Sys (`excess` / `excess_factor` en GET /online-store/districts).
     * default(0) deja las filas existentes neutras hasta la próxima sincronización.
     *
     * nullable() es obligatorio: fetchDataAndSync hace `$item[$apiField] ?? null`,
     * así que si 360Sys omite la clave escribe NULL explícito y el default nunca
     * aplica. Sin nullable, un solo distrito sin `excess` aborta toda la sync.
     * Los métodos de District normalizan el NULL con `?? 0`.
     */
    public function up(): void
    {
        Schema::table('district', function (Blueprint $table) {
            $table->decimal('excess', 10, 2)->nullable()->default(0)->after('sendCost');
            $table->unsignedInteger('excessFactor')->nullable()->default(0)->after('excess');
        });
    }

    public function down(): void
    {
        Schema::table('district', function (Blueprint $table) {
            $table->dropColumn(['excess', 'excessFactor']);
        });
    }
};
