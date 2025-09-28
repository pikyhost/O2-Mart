<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            if (!Schema::hasColumn('about_us', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('first_section_desc');
            }
            if (!Schema::hasColumn('about_us', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('about_us', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }
};
