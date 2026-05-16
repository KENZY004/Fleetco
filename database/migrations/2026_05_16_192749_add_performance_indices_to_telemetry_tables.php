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
        Schema::table('telematics_logs', function (Blueprint $table) {
            $table->index(['vehicle_id', 'captured_at']);
        });

        Schema::table('risk_events', function (Blueprint $table) {
            $table->index(['vehicle_id', 'occurred_at']);
            $table->index('resolved_at');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->index(['vehicle_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telematics_logs', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'captured_at']);
        });

        Schema::table('risk_events', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'occurred_at']);
            $table->dropIndex(['resolved_at']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'start_time']);
        });
    }
};
