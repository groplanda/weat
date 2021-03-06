<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopProducts4 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_products', function($table)
        {
            $table->integer('price')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_products', function($table)
        {
            $table->smallInteger('price')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
