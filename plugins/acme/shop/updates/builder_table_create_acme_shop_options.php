<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcmeShopOptions extends Migration
{
    public function up()
    {
        Schema::create('acme_shop_options', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
            $table->text('options')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('acme_shop_options');
    }
}
