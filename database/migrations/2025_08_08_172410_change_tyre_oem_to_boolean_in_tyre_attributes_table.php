<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tyre_attributes', function (Blueprint $table) {
            $table->boolean('tyre_oem')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tyre_attributes', function (Blueprint $table) {
            $table->string('tyre_oem')->nullable()->change();
        });
    }
};
