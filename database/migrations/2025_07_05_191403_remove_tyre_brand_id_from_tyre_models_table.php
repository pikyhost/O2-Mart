<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('tyre_models', 'tyre_brand_id')) {
            Schema::table('tyre_models', function (Blueprint $table) {
                try {
                    $table->dropForeign(['tyre_brand_id']);
                } catch (\Throwable $e) {
                }

                $table->dropColumn('tyre_brand_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tyre_models', function (Blueprint $table) {
            $table->unsignedBigInteger('tyre_brand_id')->nullable();

        });
    }
};
