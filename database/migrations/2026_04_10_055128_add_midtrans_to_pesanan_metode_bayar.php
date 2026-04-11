<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE pesanan DROP CONSTRAINT pesanan_metode_bayar_check');
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT pesanan_metode_bayar_check CHECK (metode_bayar IN ('qris', 'virtual_account', 'ewallet', 'midtrans'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pesanan DROP CONSTRAINT pesanan_metode_bayar_check');
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT pesanan_metode_bayar_check CHECK (metode_bayar IN ('qris', 'virtual_account'))");
    }
};
