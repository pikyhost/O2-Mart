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
        Schema::table('tyres', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discounted_price');
        });
    }

    public function down(): void
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }

};
