<?php namespace Acme\Setting\Models;

use Model;

/**
 * Model
 */
class Slider extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_setting_slider';


    public $attachOne = [
        'image' => 'System\Models\File'
    ];
    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
