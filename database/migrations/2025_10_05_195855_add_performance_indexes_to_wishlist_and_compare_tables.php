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
        Schema::table('wishlist_items', function (Blueprint $table) {
            if (!Schema::hasIndex('wishlist_items', 'wishlist_items_composite_index')) {
                $table->index(['wishlist_id', 'buyable_type', 'buyable_id'], 'wishlist_items_composite_index');
            }
        });
        
        Schema::table('compare_items', function (Blueprint $table) {
            if (!Schema::hasIndex('compare_items', 'compare_items_composite_index')) {
                $table->index(['compare_list_id', 'buyable_type', 'buyable_id'], 'compare_items_composite_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex('wishlist_items_composite_index');
        });
        
        Schema::table('compare_items', function (Blueprint $table) {
            $table->dropIndex('compare_items_composite_index');
        });
    }
};
