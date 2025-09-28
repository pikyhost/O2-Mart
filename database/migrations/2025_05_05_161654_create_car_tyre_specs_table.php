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
        Schema::create('car_tyre_specs', function (Blueprint $table) {
            $table->id();
            $table->string('car_make');               // اسم الشركة المصنعة
            $table->string('car_model');              // موديل السيارة
            $table->year('car_year');                 // سنة التصنيع
            $table->string('engine_performance')->nullable(); // أداء المحرك
            $table->string('tyre_size')->nullable();  // مقاس الإطار
            $table->string('tyre_oem')->nullable();   // هل الإطار أصلي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_tyre_specs');
    }
};
