<?php namespace Acme\Shop\Models;

use Model;

/**
 * Model
 */
class Option extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /*
     * Relations.
     */

    public $belongsToMany = [
        'products' => [
            'Acme\Shop\Models\Product',
            'table' => 'acme_shop_products_options'
        ]
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_shop_options';

    /**
     * @var array Validation rules
     */
    public $rules = [

    ];
}
