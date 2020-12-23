<?php namespace Acme\Shop\Models;

use Model;

/**
 * Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Sortable;

    protected $dates = ['deleted_at'];
    protected $slugs = ['slug' => 'title'];
    protected $jsonable = ['info'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'acme_shop_products';

    /**
     * @var array Validation rules
     */
    public $rules = [

    ];

    public $belongsToMany = [
        'categories' => [
            'Acme\Shop\Models\Category',
            'table' => 'acme_shop_products_categories',
        ],
        'options' => [
            'Acme\Shop\Models\Option',
            'table' => 'acme_shop_products_options',
        ],
    ];

    public $hasMany = [
        'comments' => ['Acme\Shop\Models\Comment', 'key' => 'product_id', 'otherKey' => 'id']
    ];

    public $attachMany = [
        'gallery' => ['System\Models\File', 'delete' => true ]
    ];

    public function afterDelete() {
        foreach ($this->gallery as $image) {
            $image->delete();
        }
    }

    public static $allowedSortingOptions = [
        'sort_order asc' => 'По умолчанию',
        'price asc' => 'Цена по возрастанию',
        'price desc' => 'Цена по убыванию',
        'created_at desc' => 'По новинкам',
        'sale_price desc' => 'По скидкам',
        'is_featured desc' => 'Вначале популярные',
        //'rating desc' => 'По рейтингу',
    ];

    public function scopeListFrontEnd($query, $options = [], $perPage)
    {

        extract(array_merge([
            'page'       => 1,
            'perPage'    => $perPage,
            'categories' => null,
            'colors' => null,
            'sizes' => null,
            'sort' => 'sort_order asc',
            'min' => null,
            'max' => null,
        ], $options));

        //categories
        if($categories !== null) {
            if(!is_array($categories)) {
                $categories = [$categories];
            }
            foreach($categories as $category) {
                $query->whereHas('categories', function($q) use ($category) {
                    $q->where('id', '=', $category);
                });
            }
        }

        //colors
        if(!empty($colors)) {
            foreach($colors as $color) {
                $query->whereHas('options', function($q) use ($colors) {
                    $q->whereIn('id', $colors);
                });
            }
        }

        //sizes
        if(!empty($sizes)) {
            foreach($sizes as $size) {
                $query->whereHas('options', function($q) use ($sizes) {
                    $q->whereIn('id', $sizes);
                });
            }
        }

        //sortable
        if(!is_array($sort)) {
            $sort = [$sort];
        }
        foreach($sort as $_sort) {
            if(in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if(count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirecton) = $parts;
                if($sortField === 'rating') {
                    $query->whereHas('comments', function($q) {
                        $q->sortBy('avg(rating)')->orderBy($sortField, 'desc');
                    });
                } else {
                    $query->orderBy($sortField, $sortDirecton);
                }

            }
        }

        //price
        if($min !== null && $max !== null) {
            $query->whereBetween('price', [$min, $max]);
        }

        $lastPage = $query->paginate($perPage, $page)->lastPage();

        if($lastPage < $page){
            $page = 1;
        }

        return $query->paginate($perPage, $page);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', '1');
    }

}
