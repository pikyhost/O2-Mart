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
            $table->foreignId('tyre_size_id')->nullable()->constrained('tyre_sizes')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('tyres', function (Blueprint $table) {
            $table->dropForeign(['tyre_size_id']);
            $table->dropColumn('tyre_size_id');
        });
    }

};
