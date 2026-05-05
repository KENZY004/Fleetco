<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, int, boolean, json
            $table->timestamps();
        });

        // Seed initial settings
        DB::table('settings')->insert([
            ['key' => 'company_name', 'value' => 'Fleetco Logistics', 'type' => 'string'],
            ['key' => 'speed_limit', 'value' => '80', 'type' => 'int'],
            ['key' => 'simulation_enabled', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'alert_email', 'value' => 'admin@fleetco.com', 'type' => 'string'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
