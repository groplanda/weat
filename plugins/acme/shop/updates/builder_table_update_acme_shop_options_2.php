<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopOptions2 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_options', function($table)
        {
            $table->string('type')->change();
            $table->dropColumn('title');
            $table->dropColumn('options');
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_options', function($table)
        {
            $table->string('type', 191)->change();
            $table->string('title', 191)->nullable();
            $table->text('options')->nullable();
        });
    }
}
