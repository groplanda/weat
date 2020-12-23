<?php namespace Acme\Shop\Models;

use Model;

/**
 * Model
 */
class Comment extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_shop_comments';

    public $belongsTo = [
        'product' => 'Acme\Shop\Models\Product'
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
