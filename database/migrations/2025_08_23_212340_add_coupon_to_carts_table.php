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
        Schema::table('carts', function (Blueprint $table) {
            $table->string('applied_coupon')->nullable()->after('area_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('applied_coupon');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['applied_coupon', 'discount_amount']);
        });
    }

};
