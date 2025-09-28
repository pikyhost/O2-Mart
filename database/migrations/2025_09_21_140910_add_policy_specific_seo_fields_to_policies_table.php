<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->string('meta_title_privacy_policy')->nullable();
            $table->text('meta_description_privacy_policy')->nullable();
            $table->string('alt_text_privacy_policy')->nullable();
            
            $table->string('meta_title_refund_policy')->nullable();
            $table->text('meta_description_refund_policy')->nullable();
            $table->string('alt_text_refund_policy')->nullable();
            
            $table->string('meta_title_terms_of_service')->nullable();
            $table->text('meta_description_terms_of_service')->nullable();
            $table->string('alt_text_terms_of_service')->nullable();
        });
    }

    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title_privacy_policy', 'meta_description_privacy_policy', 'alt_text_privacy_policy',
                'meta_title_refund_policy', 'meta_description_refund_policy', 'alt_text_refund_policy',
                'meta_title_terms_of_service', 'meta_description_terms_of_service', 'alt_text_terms_of_service'
            ]);
        });
    }
};