<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_car_compatibility', function (Blueprint $table) {
            $table->foreignId('car_model_id')->after('product_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_car_compatibility', function (Blueprint $table) {
            $table->dropForeign(['car_model_id']);
            $table->dropColumn('car_model_id');
        });
    }

};
