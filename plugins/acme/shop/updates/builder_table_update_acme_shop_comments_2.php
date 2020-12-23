<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopComments2 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_comments', function($table)
        {
            $table->boolean('status')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_comments', function($table)
        {
            $table->dropColumn('status');
        });
    }
}
