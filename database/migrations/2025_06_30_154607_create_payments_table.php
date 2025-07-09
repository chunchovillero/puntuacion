<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchday_participant_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // ID de transacción de WebPay
            $table->string('order_id')->unique(); // ID de orden interno
            $table->decimal('amount', 8, 2); // Monto del pago
            $table->string('currency', 3)->default('CLP'); // Moneda
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('payment_method')->default('webpay'); // webpay, credit_card, etc.
            $table->string('authorization_code')->nullable(); // Código de autorización
            $table->string('response_code')->nullable(); // Código de respuesta
            $table->json('webpay_response')->nullable(); // Respuesta completa de WebPay
            $table->timestamp('paid_at')->nullable(); // Fecha de pago exitoso
            $table->string('payer_email')->nullable(); // Email del pagador
            $table->string('payer_name')->nullable(); // Nombre del pagador
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
