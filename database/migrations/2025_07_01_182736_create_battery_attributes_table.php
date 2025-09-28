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
        Schema::create('battery_attributes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('car_make_id')->constrained()->cascadeOnDelete();
        $table->foreignId('car_model_id')->constrained()->cascadeOnDelete();
        $table->year('model_year');
        $table->string('name'); 
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_attributes');
    }
};
