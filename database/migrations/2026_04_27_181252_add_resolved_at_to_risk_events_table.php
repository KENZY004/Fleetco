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
        Schema::table('risk_events', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->after('impact_score');
        });
    }

    public function down(): void
    {
        Schema::table('risk_events', function (Blueprint $table) {
            $table->dropColumn('resolved_at');
        });
    }
};
