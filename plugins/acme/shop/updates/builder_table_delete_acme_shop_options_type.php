<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteAcmeShopOptionsType extends Migration
{
    public function up()
    {
        Schema::dropIfExists('acme_shop_options_type');
    }
    
    public function down()
    {
        Schema::create('acme_shop_options_type', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('option_id')->unsigned();
            $table->string('type', 191)->nullable();
        });
    }
}
