<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->unsignedBigInteger('buyable_id');
            $table->string('buyable_type');
            $table->index(['buyable_id', 'buyable_type']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['buyable_id', 'buyable_type']);
            $table->dropColumn(['buyable_id', 'buyable_type']);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
        });
    }

};
