<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indicates if the migration should be run within a transaction.
     *
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->decimal('risk_score', 5, 2)->default(100.00);
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_plate')->unique();
            $table->enum('status', ['active', 'idle', 'maintenance', 'offline'])->default('offline');
            $table->foreignId('current_driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('telematics_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            
            // Spatial Intelligence: Using Geography point for global GPS coords
            if (DB::getDriverName() === 'sqlite') {
                $table->text('location');
            } else {
                $table->geography('location', subtype: 'point', srid: 4326);
            }
            
            $table->float('speed')->default(0); // km/h
            $table->float('heading')->default(0); // degrees
            $table->timestamp('captured_at');
            $table->timestamps();

        });

        // Use raw SQL for spatial index to avoid grammar conflicts
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('CREATE INDEX telematics_location_spatial_index ON telematics_logs USING GIST (location)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telematics_logs');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
    }
};
