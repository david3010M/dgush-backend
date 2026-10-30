<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Agencias de envío interdepartamental (modo ENVIO).
     *
     * Llegan desde 360Sys en GET /online-store/districts (clave `agencies`).
     * shipping_cost => true: la agencia cobra envío + exceso del distrito.
     *                  false: el envío se paga en destino, la tienda cobra 0.
     *
     * Columnas nullable: fetchDataAndSync escribe NULL si 360Sys omite la clave.
     * sort_order guarda el `order` de 360Sys (`order` es palabra reservada en SQL).
     */
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('shipping_cost')->nullable()->default(true);
            $table->boolean('status')->nullable()->default(true);
            $table->unsignedInteger('sort_order')->nullable()->default(0);
            $table->unsignedBigInteger('server_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
