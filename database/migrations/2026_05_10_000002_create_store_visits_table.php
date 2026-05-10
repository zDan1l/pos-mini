<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_visits', function (Blueprint $table) {
            $table->id('idvisit');
            $table->foreignId('idtoko')->constrained('stores', 'idtoko')->onDelete('cascade');
            $table->foreignId('iduser')->constrained('users', 'id')->onDelete('cascade');
            $table->decimal('visit_latitude', 10, 8);
            $table->decimal('visit_longitude', 11, 8);
            $table->decimal('visit_accuracy', 10, 2);
            $table->decimal('distance_from_store', 10, 2)->comment('Distance in meters');
            $table->enum('status', ['diterima', 'ditolak'])->default('diterima');
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['idtoko', 'iduser']);
            $table->index('visited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_visits');
    }
};
