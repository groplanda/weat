<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcmeShopComments extends Migration
{
    public function up()
    {
        Schema::create('acme_shop_comments', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('email')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('acme_shop_comments');
    }
}
