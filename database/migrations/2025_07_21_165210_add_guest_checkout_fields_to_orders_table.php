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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->string('session_id')->nullable()->index();

            $table->string('car_make')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_year')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('vin')->nullable();

            // Rename shipping_method → shipping_option for consistency
            $table->renameColumn('shipping_method', 'shipping_option');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'contact_name',
                'contact_email',
                'contact_phone',
                'session_id',
                'car_make',
                'car_model',
                'car_year',
                'plate_number',
                'vin',
            ]);

            $table->renameColumn('shipping_option', 'shipping_method');
        });
    }

};
