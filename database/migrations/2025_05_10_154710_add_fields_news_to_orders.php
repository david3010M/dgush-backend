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
        Schema::table('order', function (Blueprint $table) {
          
            //SOLO OPCIONALES
            $table->string('number')->nullable()->unique()->change();
            $table->decimal('subtotal', 10)->nullable()->change();
            $table->decimal('discount', 10)->nullable()->change();
            $table->decimal('sendCost', 10)->nullable()->change();
            $table->decimal('total', 10)->nullable()->change();
            $table->decimal('quantity', 10)->nullable()->change();
            $table->timestamp('date')->nullable()->change();
            $table->string('status')->nullable()->change();
      


            //NUEVOS CAMPOS 
            $table->string('stage')->nullable();
    
            $table->string('server_id')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('end_date')->nullable();
   
            $table->string('mode')->nullable();
            $table->string('cellphone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('address')->nullable();

            $table->string('destiny')->nullable();
            $table->foreignId('zone_id')->nullable()->constrained('zones');
            $table->foreignId('district_id')->nullable()->constrained('district');

            $table->foreignId('branch_id')->nullable()->constrained('sedes');

            $table->json('customer')->nullable();

            $table->string('notes')->nullable();
            $table->string('currency')->nullable();

            $table->json('payments')->nullable();
            $table->json('products')->nullable();


        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            // Eliminar las claves foráneas
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['coupon_id']);
    
            // Eliminar columnas
            $table->dropColumn([
                'stage',
 
                'server_id',
                'scheduled_date',
                'payment_date',
                'end_date',
                'mode',
                'cellphone_number',
                'email_address',
                'address',
                'destiny',
                'zone_id',
                'district_id',
                'coupon_id',
                'branch_id',
                'customer',
                'notes',
                'currency',
                'payments',
                'products',
            ]);
        });
    }
};
