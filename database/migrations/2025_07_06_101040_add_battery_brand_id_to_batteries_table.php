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
        Schema::table('batteries', function (Blueprint $table) {
            $table->foreignId('battery_brand_id')->nullable()->constrained('battery_brands')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropForeign(['battery_brand_id']);
            $table->dropColumn('battery_brand_id');
        });
    }

};
