<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopOptionsType3 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_options_type', function($table)
        {
            $table->renameColumn('id', 'option_id');
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_options_type', function($table)
        {
            $table->renameColumn('option_id', 'id');
        });
    }
}
