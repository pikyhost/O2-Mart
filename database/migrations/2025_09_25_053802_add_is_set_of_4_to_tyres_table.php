<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->boolean('is_set_of_4')->default(false)->after('buy_3_get_1_free');
        });
    }

    public function down(): void
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->dropColumn('is_set_of_4');
        });
    }
};