<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_models', function (Blueprint $table) {
            $table->year('year_from')->nullable();
            $table->year('year_to')->nullable();
            $table->string('generation')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('engine_size', 4, 1)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('car_models', function (Blueprint $table) {
            $table->dropColumn([
                'year_from', 'year_to', 'generation', 'fuel_type', 'engine_size', 'is_active'
            ]);
        });
    }

};
