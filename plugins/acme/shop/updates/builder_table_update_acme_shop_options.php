<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopOptions extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_options', function($table)
        {
            $table->string('type')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_options', function($table)
        {
            $table->dropColumn('type');
        });
    }
}
