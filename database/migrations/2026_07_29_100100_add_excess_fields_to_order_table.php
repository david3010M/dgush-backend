<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Congela el desglose del exceso de envío en la orden, para poder re-mostrarlo
     * sin recalcular con datos del distrito que pudieron cambiar después.
     *
     * excessAmount => monto total de exceso cobrado (ya incluido en order.sendCost).
     * excessUnits  => cuántas veces se aplicó el monto unitario.
     */
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->decimal('excessAmount', 10, 2)->default(0)->after('sendCost');
            $table->unsignedInteger('excessUnits')->default(0)->after('excessAmount');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['excessAmount', 'excessUnits']);
        });
    }
};
