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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();

            // Foreign Keys (one will be null, the other filled)
            $table->foreignId('installer_shop_id')
                ->nullable()
                ->constrained('installer_shops')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('mobile_van_service_id')
                ->nullable()
                ->constrained('mobile_van_services')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('day_id')->constrained('days')->cascadeOnUpdate();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
