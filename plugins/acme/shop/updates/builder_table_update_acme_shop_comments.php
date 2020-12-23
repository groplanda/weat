<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopComments extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_comments', function($table)
        {
            $table->integer('rating')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_comments', function($table)
        {
            $table->dropColumn('rating');
        });
    }
}
