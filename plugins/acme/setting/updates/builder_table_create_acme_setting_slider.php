<?php namespace Acme\Setting\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcmeSettingSlider extends Migration
{
    public function up()
    {
        Schema::create('acme_setting_slider', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->string('description');
            $table->string('link');
            $table->string('color');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('acme_setting_slider');
    }
}
