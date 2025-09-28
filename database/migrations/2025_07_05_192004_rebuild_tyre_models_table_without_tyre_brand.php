<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tyre_models_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::statement('INSERT INTO tyre_models_temp (id, name, created_at, updated_at) SELECT id, name, created_at, updated_at FROM tyre_models');

        Schema::drop('tyre_models');

        Schema::rename('tyre_models_temp', 'tyre_models');
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tyre_models');
    }

};
