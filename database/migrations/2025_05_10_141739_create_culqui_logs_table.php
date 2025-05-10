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
        Schema::create('culqui_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('record_id')->nullable();    // id del campo observado
            $table->integer('server_id')->nullable();    // id 360 del campo observado
            $table->string('action')->nullable();        // Acción realizada (create, update, delete)
            $table->string('table_name')->nullable();    // Nombre de la tabla afectada
            $table->json('request')->nullable();         // Datos en formato JSON (para almacenar request recibido)
            $table->json('response')->nullable();        //  Datos en formato JSON (para almacenar respuesta)
            $table->ipAddress('ip_address')->nullable(); // Dirección IP del usuario
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('culqui_logs');
    }
};
