<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('extra_per_kg', 8, 2)->default(2);
            $table->decimal('fuel_percent', 5, 4)->default(0.02);
            $table->decimal('packaging_fee', 8, 2)->default(5.25);
            $table->decimal('epg_percent', 5, 4)->default(0.10);
            $table->decimal('epg_min', 8, 2)->default(2);
            $table->decimal('vat_percent', 5, 4)->default(0.05);
            $table->integer('volumetric_divisor')->default(5000);
            $table->json('tiers')->nullable(); 
            $table->json('remote_areas')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
