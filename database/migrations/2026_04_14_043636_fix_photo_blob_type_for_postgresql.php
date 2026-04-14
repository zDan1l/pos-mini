<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, use BYTEA type explicitly
        // Drop existing column and recreate with proper type
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            Schema::table('users', function ($table) {
                $table->dropColumn('photo_blob');
            });

            DB::statement('ALTER TABLE users ADD COLUMN photo_blob BYTEA NULL');
        } else {
            // For MySQL/MariaDB, binary type works fine
            Schema::table('users', function ($table) {
                $table->binary('photo_blob')->nullable()->after('photo_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('photo_blob');
        });

        // Recreate with original binary type
        Schema::table('users', function ($table) {
            $table->binary('photo_blob')->nullable()->after('photo_path');
        });
    }
};
