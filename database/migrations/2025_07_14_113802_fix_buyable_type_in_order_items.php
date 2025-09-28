<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_items')
            ->where('buyable_type', 'autoPart')
            ->update(['buyable_type' => 'App\Models\AutoPart']);

        DB::table('order_items')
            ->where('buyable_type', 'battery')
            ->update(['buyable_type' => 'App\Models\Battery']);

        DB::table('order_items')
            ->where('buyable_type', 'tyre')
            ->update(['buyable_type' => 'App\Models\Tyre']);

        DB::table('order_items')
            ->where('buyable_type', 'rim')
            ->update(['buyable_type' => 'App\Models\Rim']);
    }

    public function down(): void
    {
        DB::table('order_items')
            ->where('buyable_type', 'App\Models\AutoPart')
            ->update(['buyable_type' => 'autoPart']);

        DB::table('order_items')
            ->where('buyable_type', 'App\Models\Battery')
            ->update(['buyable_type' => 'battery']);

        DB::table('order_items')
            ->where('buyable_type', 'App\Models\Tyre')
            ->update(['buyable_type' => 'tyre']);

        DB::table('order_items')
            ->where('buyable_type', 'App\Models\Rim')
            ->update(['buyable_type' => 'rim']);
    }
};
