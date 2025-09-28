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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('shipping_option')->default('delivery_only');
            $table->unsignedBigInteger('mobile_van_id')->nullable();
            $table->unsignedBigInteger('installation_center_id')->nullable();
            $table->date('installation_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_option',
                'mobile_van_id',
                'installation_center_id',
                'installation_date',
            ]);
        });
    }
};
