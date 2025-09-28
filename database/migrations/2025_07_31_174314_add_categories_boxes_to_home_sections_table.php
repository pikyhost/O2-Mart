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
        // categories_boxes column already added in 2025_07_27_202132_add_categories_boxes_to_home_sections_table.php
        // Nothing to do here
    }

    public function down()
    {
        // Nothing to rollback since no columns were added
    }

};
