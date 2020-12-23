<?php namespace Acme\Setting\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeSettingSlider2 extends Migration
{
    public function up()
    {
        Schema::table('acme_setting_slider', function($table)
        {
            $table->integer('sort_order')->nullable()->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('acme_setting_slider', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
