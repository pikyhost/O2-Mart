<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('product_sections', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (!Schema::hasColumn('product_sections', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (!Schema::hasColumn('product_sections', 'alt_text')) {
                $table->string('alt_text')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }
};