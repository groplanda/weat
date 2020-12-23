<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopOptionsType2 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_options_type', function($table)
        {
            $table->renameColumn('title', 'type');
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_options_type', function($table)
        {
            $table->renameColumn('type', 'title');
        });
    }
}
