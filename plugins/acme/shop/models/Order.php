<?php namespace Acme\Shop\Models;

use Model;

/**
 * Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $attachOne  = [
        'image' => ['System\Models\File', 'delete' => true ]
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_shop_orders';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    protected $jsonable = ['products'];
}
