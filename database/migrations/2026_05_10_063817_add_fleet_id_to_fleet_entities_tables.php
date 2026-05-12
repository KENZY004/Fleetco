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
        // Add fleet_id to drivers table for multi-tenant scoping
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('fleet_id')->nullable()->constrained('fleets')->nullOnDelete()->after('id');
        });

        // Add fleet_id to vehicles table for multi-tenant scoping
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('fleet_id')->nullable()->constrained('fleets')->nullOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['fleet_id']);
            $table->dropColumn('fleet_id');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['fleet_id']);
            $table->dropColumn('fleet_id');
        });
    }
};
