<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::table('auto_parts', function (Blueprint $table) {
            $table->foreignId('auto_part_brand_id')->nullable()->constrained('auto_part_brands')->nullOnDelete();
            $table->foreignId('auto_part_country_id')->nullable()->constrained('auto_part_countries')->nullOnDelete();
            $table->foreignId('viscosity_grade_id')->nullable()->constrained('viscosity_grades')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('auto_parts', function (Blueprint $table) {
            $table->dropForeign(['auto_part_brand_id']);
            $table->dropColumn('auto_part_brand_id');

            $table->dropForeign(['auto_part_country_id']);
            $table->dropColumn('auto_part_country_id');

            $table->dropForeign(['viscosity_grade_id']);
            $table->dropColumn('viscosity_grade_id');
        });
    }

};
