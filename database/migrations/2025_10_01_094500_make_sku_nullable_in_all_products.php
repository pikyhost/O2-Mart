<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
        });

        Schema::table('rims', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
        });

        Schema::table('tyres', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
        });

        Schema::table('auto_parts', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
        });

        Schema::table('rims', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
        });

        Schema::table('tyres', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
        });

        Schema::table('auto_parts', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
        });
    }
};