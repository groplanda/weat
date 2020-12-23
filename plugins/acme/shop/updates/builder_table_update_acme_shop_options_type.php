<?php namespace Acme\Shop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeShopOptionsType extends Migration
{
    public function up()
    {
        Schema::rename('acme_shop_options_description', 'acme_shop_options_type');
    }
    
    public function down()
    {
        Schema::rename('acme_shop_options_type', 'acme_shop_options_description');
    }
}
