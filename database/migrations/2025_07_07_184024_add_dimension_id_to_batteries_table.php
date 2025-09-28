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
        Schema::table('batteries', function (Blueprint $table) {
            $table->foreignId('dimension_id')->nullable()->constrained('battery_dimensions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropForeign(['dimension_id']);
            $table->dropColumn('dimension_id');
        });
    }

};
