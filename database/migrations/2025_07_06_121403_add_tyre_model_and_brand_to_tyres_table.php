<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->foreignId('tyre_model_id')->nullable()->constrained()->after('tyre_size_id');
            $table->foreignId('tyre_brand_id')->nullable()->constrained()->after('tyre_model_id');
        });
    }

    public function down()
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->dropForeign(['tyre_model_id']);
            $table->dropColumn('tyre_model_id');

            $table->dropForeign(['tyre_brand_id']);
            $table->dropColumn('tyre_brand_id');
        });
    }

};
