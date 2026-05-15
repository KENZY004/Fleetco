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
        Schema::table('driver_duty_logs', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('driver_telemetry', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_duty_logs', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')
                ->references('id')
                ->on('users');
        });

        Schema::table('driver_telemetry', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')
                ->references('id')
                ->on('users');
            
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles');
        });
    }
};
