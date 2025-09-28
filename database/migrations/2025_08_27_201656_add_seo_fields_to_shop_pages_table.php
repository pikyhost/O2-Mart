<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shop_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_pages', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('section_2_image');
            }
            if (!Schema::hasColumn('shop_pages', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('shop_pages', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shop_pages', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }
};
