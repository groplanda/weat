<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcmeShopOptionsDescription extends Migration
{
    public function up()
    {
        Schema::create('acme_shop_options_description', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('acme_shop_options_description');
    }
}
