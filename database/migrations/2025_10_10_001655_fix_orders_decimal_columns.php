<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->nullable()->change();
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
            $table->decimal('tax_amount', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shipping_cost')->nullable()->change();
            $table->unsignedInteger('subtotal')->default(0)->change();
            $table->unsignedInteger('total')->default(0)->change();
            $table->integer('tax_amount')->default(0)->change();
        });
    }
};