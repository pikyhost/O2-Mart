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
        Schema::table('rims', function (Blueprint $table) {
            $table->foreignId('rim_brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rim_size_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rim_country_id')->nullable()->constrained()->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rims', function (Blueprint $table) {
            //
        });
    }
};
