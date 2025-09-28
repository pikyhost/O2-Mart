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
    if (!Schema::hasColumn('tyres', 'meta_title')) {
        $table->string('meta_title')->nullable()->after('title');
    }
    if (!Schema::hasColumn('tyres', 'meta_description')) {
        $table->text('meta_description')->nullable()->after('meta_title');
    }
    if (!Schema::hasColumn('tyres', 'alt_text')) {
        $table->string('alt_text')->nullable()->after('meta_description');
    }
});

    }

    public function down(): void
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'alt_text']);
        });
    }

};
