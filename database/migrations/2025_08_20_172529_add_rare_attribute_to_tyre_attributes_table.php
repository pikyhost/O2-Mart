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
        Schema::table('tyre_attributes', function (Blueprint $table) {
            $table->string('rare_attribute')->nullable()->after('tyre_attribute');
        });
    }

    public function down(): void
    {
        Schema::table('tyre_attributes', function (Blueprint $table) {
            $table->dropColumn('rare_attribute');
        });
    }

};
