<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Indexes already exist in production, skip this migration
        return;
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['session_id']);
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex(['wishlist_id']);
            $table->dropIndex(['buyable_type', 'buyable_id']);
            $table->dropIndex(['wishlist_id', 'buyable_type', 'buyable_id']);
        });
    }
};