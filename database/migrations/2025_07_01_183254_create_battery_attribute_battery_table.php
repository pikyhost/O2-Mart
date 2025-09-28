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
        Schema::create('battery_attribute_battery', function (Blueprint $table) {
        $table->id();
        $table->foreignId('battery_id')->constrained()->cascadeOnDelete();
        $table->foreignId('battery_attribute_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_attribute_battery');
    }
};
