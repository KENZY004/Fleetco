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
        Schema::create('landmarks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['depot', 'client', 'restricted', 'optimized_route'])->default('client');
            
            // Spatial Intelligence: Using Geography for complex geofence shapes
            if (DB::getDriverName() === 'sqlite') {
                $table->text('area');
            } else {
                $table->geography('area', subtype: 'polygon', srid: 4326);
            }
            
            $table->json('metadata')->nullable(); // For customer info, notes
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('CREATE INDEX landmarks_area_spatial_index ON landmarks USING GIST (area)');
        }

        Schema::create('risk_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('telematics_log_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('type'); // speeding, idling, geofence_breach
            $table->float('impact_score'); // Negative impact on total score
            $table->json('details')->nullable(); // Meta info like "Speeded by 20km/h"
            
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_events');
        Schema::dropIfExists('landmarks');
    }
};
