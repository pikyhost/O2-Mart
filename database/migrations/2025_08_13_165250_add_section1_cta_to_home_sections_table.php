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
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('section_1_cta_text')->nullable();
            $table->string('section_1_cta_link')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn(['section_1_cta_text','section_1_cta_link']);
        });
    }
};
