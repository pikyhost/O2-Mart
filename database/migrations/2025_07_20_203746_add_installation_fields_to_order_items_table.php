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
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('shipping_option', ['delivery_only', 'with_installation', 'installation_center'])->nullable()->after('sku');
            $table->unsignedBigInteger('installation_center_id')->nullable()->after('shipping_option');
            $table->unsignedBigInteger('mobile_van_id')->nullable()->after('installation_center_id');
            $table->date('installation_date')->nullable()->after('mobile_van_id');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['shipping_option', 'installation_center_id', 'mobile_van_id', 'installation_date']);
        });
    }

};
