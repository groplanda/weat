<?php namespace Acme\Contactform\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcmeContactformOrder extends Migration
{
    public function up()
    {
        Schema::table('acme_contactform_order', function($table)
        {
            $table->string('user_town', 191)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('acme_contactform_order', function($table)
        {
            $table->dropColumn('user_town');
        });
    }
}
