<?php namespace Acme\Setting\Models;

use Model;

/**
 * Model
 */
class Advantage extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_setting_advantage';

    protected $jsonable = ['advantages'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
