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
            Schema::table('tyres', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
                $table->string('image')->nullable()->change();
                $table->decimal('wheel_diameter', 8, 2)->nullable()->change();
                $table->string('load_index')->nullable()->change();
                $table->string('speed_rating')->nullable()->change();
                $table->decimal('weight_kg', 8, 2)->nullable()->change();
                $table->string('alt_text')->nullable()->change();
                $table->string('warranty')->nullable()->change();

                $table->unsignedBigInteger('tyre_size_id')->nullable()->change();
                $table->unsignedBigInteger('tyre_model_id')->nullable()->change();
                $table->unsignedBigInteger('tyre_attribute_id')->nullable()->change();
                $table->unsignedBigInteger('tyre_country_id')->nullable()->change();

                $table->year('production_year')->nullable()->change();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tyres', function (Blueprint $table) {
            //
        });
    }
};
