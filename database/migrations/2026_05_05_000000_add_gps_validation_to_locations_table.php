<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Add GPS validation requirement flag
            $table->boolean('require_gps_validation')->default(true)->after('radius_meters')->comment('Require GPS validation saat scan QR code');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['require_gps_validation']);
        });
    }
};
