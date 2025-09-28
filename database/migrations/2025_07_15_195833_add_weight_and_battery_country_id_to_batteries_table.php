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
        Schema::table('batteries', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('dimension_id');
            $table->unsignedBigInteger('battery_country_id')->nullable()->after('weight');

            $table->foreign('battery_country_id')->references('id')->on('battery_countries')->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            //
        });
    }
};
