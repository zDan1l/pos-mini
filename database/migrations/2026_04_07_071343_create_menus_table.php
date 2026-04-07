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
        Schema::create('menus', function (Blueprint $table) {
            $table->id('idmenu');
            $table->string('nama_menu');
            $table->decimal('harga', 10, 2);
            $table->string('path_gambar')->nullable();
            $table->unsignedBigInteger('idvendor');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('idvendor')->references('idvendor')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
