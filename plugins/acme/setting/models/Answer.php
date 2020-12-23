<?php namespace Acme\Setting\Models;

use Model;

/**
 * Model
 */
class Answer extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_setting_faq';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
