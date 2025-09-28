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
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->integer('shipping_cost')->default(0);
            $table->string('shipping_estimate_time')->default('0-0');
            $table->timestamps();

            $table->boolean('is_active')->default(true)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
