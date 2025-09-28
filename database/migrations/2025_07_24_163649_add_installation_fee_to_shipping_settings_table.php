<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->decimal('installation_fee', 8, 2)->default(200)->after('volumetric_divisor');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            //
        });
    }
};
