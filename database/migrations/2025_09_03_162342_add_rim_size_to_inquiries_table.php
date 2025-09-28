<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('rim_size')->nullable()->after('rim_size_id');
            $table->dropForeign(['rim_size_id']);
            $table->dropColumn('rim_size_id');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn('rim_size');
            $table->foreignId('rim_size_id')->nullable()->constrained('rim_sizes');
        });
    }
};