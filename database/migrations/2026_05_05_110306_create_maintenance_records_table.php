<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('service_type'); // Oil Change, Brake Check, etc.
            $table->decimal('odometer_reading', 12, 2);
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('service_date');
            $table->decimal('next_service_at_km', 12, 2)->nullable();
            $table->date('next_service_due_date')->nullable();
            $table->timestamps();
        });

        // Also add an 'odometer' column to vehicles to track current mileage
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('odometer', 12, 2)->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('odometer');
        });
    }
};
