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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->string('nama');
            $table->timestamp('timestamp');
            $table->decimal('total', 10, 2);
            $table->enum('metode_bayar', ['qris', 'virtual_account']);
            $table->enum('status_bayar', ['pending', 'lunas', 'expired'])->default('pending');
            $table->string('payment_reference')->unique()->nullable();
            $table->unsignedBigInteger('idcustomer');
            $table->unsignedBigInteger('idvendor');
            $table->timestamps();

            $table->foreign('idcustomer')->references('idcustomer')->on('customers');
            $table->foreign('idvendor')->references('idvendor')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
