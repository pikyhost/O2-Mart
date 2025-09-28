<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('batteries', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });

        Schema::table('batteries', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('batteries', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });

        Schema::table('batteries', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->restrictOnDelete();
        });
    }
};
