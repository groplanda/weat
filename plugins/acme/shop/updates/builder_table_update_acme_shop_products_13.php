<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopProducts13 extends Migration
{
    public function up()
    {
        Schema::table('acme_shop_products', function($table)
        {
            $table->dropColumn('image');
            $table->dropColumn('rating');
            $table->dropColumn('iframe');
            $table->dropColumn('is_iframe');
            $table->dropColumn('options');
        });
    }
    
    public function down()
    {
        Schema::table('acme_shop_products', function($table)
        {
            $table->string('image', 191)->nullable();
            $table->integer('rating')->nullable();
            $table->text('iframe')->nullable();
            $table->boolean('is_iframe')->nullable()->default(0);
            $table->text('options')->nullable();
        });
    }
}
