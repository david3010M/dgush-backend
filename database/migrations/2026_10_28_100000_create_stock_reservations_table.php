<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reservas de stock del chatbot, solo a nivel de tienda virtual.
     *
     * 360Sys es dueño de product_details.stock y lo sobrescribe en cada sync,
     * así que la reserva vive aparte y nunca toca esa columna:
     * disponible = max(stock - reservas vigentes, 0) (StockReservationService).
     *
     * Vigente = status 'activa' y expires_at_utc > UTC_TIMESTAMP(). Se guarda en
     * UTC para no depender de la zona horaria de la sesión MySQL.
     */
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100); // whatsapp_order_id del agente
            $table->foreignId('product_id')->constrained('product');
            $table->foreignId('color_id')->constrained('color');
            $table->foreignId('size_id')->constrained('size');
            $table->unsignedInteger('quantity');
            $table->string('status', 20)->default('activa'); // activa, convertida, liberada
            $table->dateTime('expires_at_utc');
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'color_id', 'size_id', 'status', 'expires_at_utc'], 'stock_reservations_variant_idx');
            $table->index(['reference', 'status']);
        });

        // Respuesta guardada por Idempotency-Key: un reintento devuelve lo mismo sin reaplicar.
        Schema::create('chatbot_idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 100);
            $table->string('endpoint', 50);
            $table->char('request_hash', 64);
            // longText y no json: MySQL reordena las claves del tipo json y el replay saldría distinto.
            $table->longText('response')->nullable();
            $table->timestamps();

            $table->unique(['idempotency_key', 'endpoint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_idempotency_keys');
        Schema::dropIfExists('stock_reservations');
    }
};
