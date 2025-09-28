<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->string('cta_text')->nullable()->default(null)->change();
            $table->string('cta_link')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->string('cta_text')->nullable(false)->change();
            $table->string('cta_link')->nullable(false)->change();
        });
    }
};
