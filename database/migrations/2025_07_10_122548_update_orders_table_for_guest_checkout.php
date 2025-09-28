<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method')->nullable()->after('guest_phone');
            $table->string('payment_method')->nullable()->after('shipping_method');

            // Rename aramex_tracking_url to tracking_url
            if (Schema::hasColumn('orders', 'aramex_tracking_url')) {
                $table->renameColumn('aramex_tracking_url', 'tracking_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'payment_method']);

            if (Schema::hasColumn('orders', 'tracking_url')) {
                $table->renameColumn('tracking_url', 'aramex_tracking_url');
            }
        });
    }
};
