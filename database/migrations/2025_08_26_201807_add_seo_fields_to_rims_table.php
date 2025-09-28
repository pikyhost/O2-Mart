<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rims', function (Blueprint $table) {
            if (!Schema::hasColumn('rims', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('rims', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('rims', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rims', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }
};

