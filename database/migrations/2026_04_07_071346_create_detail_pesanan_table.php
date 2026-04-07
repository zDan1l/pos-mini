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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('iddetail_pesanan');
            $table->unsignedBigInteger('idmenu');
            $table->unsignedBigInteger('idpesanan');
            $table->integer('jumlah');
            $table->decimal('harga', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamp('timestamp');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('idmenu')->references('idmenu')->on('menus')->onDelete('cascade');
            $table->foreign('idpesanan')->references('idpesanan')->on('pesanan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
