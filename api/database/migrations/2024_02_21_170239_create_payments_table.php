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
            $table->uuid('id')->primary();
            $table->float('transaction_amount');
            $table->integer('installments');
            $table->string('token');
            $table->string('payment_method_id');
            $table->string('payer_entity_type')->default('individual');
            $table->string('payer_type')->default('customer');
            $table->string('payer_email');
            $table->string('payer_identification_type');
            $table->string('payer_identification_number');
            $table->string('notification_url');
            $table->enum('status', ['PENDING', 'PAID', 'CANCELED'])->default('PENDING');
            $table->timestamps();
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
