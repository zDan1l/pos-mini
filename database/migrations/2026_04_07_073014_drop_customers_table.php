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
        // Drop foreign key constraint first
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['idcustomer']);
            $table->dropColumn('idcustomer');
        });

        // Then drop the customers table
        Schema::dropIfExists('customers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('idcustomer');
            $table->string('nama_customer')->unique();
            $table->string('tipe')->default('guest');
            $table->timestamps();
        });

        // Re-add the foreign key
        Schema::table('pesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('idcustomer')->nullable();
            $table->foreign('idcustomer')->references('idcustomer')->on('customers');
        });
    }
};
