<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('home_sections', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('blog_section_text');
            }
            if (!Schema::hasColumn('home_sections', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('home_sections', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }
};

