<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropColumn([
                'brand_name',
                'parent_category_name',
                'sub_category_name',
                'country_name',
                'dimensions',
                'weight',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->string('brand_name')->nullable();
            $table->string('parent_category_name')->nullable();
            $table->string('sub_category_name')->nullable();
            $table->string('country_name')->nullable();
            $table->string('dimensions')->nullable();
            $table->decimal('weight', 6, 2)->nullable();
        });
    }
};
