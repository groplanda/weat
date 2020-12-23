<?php namespace Acme\Shop\Components;

use Acme\Shop\Models\Product;
use Acme\Shop\Models\Category;
use Acme\Shop\Models\Option;

class ProductList extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Список товаров',
            'description' => 'Показать товары'
        ];
    }

    public function defineProperties()
    {
        return [
            'maxItems' => [
                'title'             => 'Товаров на странице',
                'default'           => 6,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Можно использовать только цифры'
            ]
        ];
    }

    function prepareVars()
    {
        $cat_param = $this->param('slug');
        $options = post('filter', []);

        //filters
        $this->page['maxPrice'] = Product::active()->max('price');
        $this->page['minPrice'] = Product::active()->min('price');

        $this->page['colors'] = Option::where('type', 'color')->get();
        $this->page['sizes'] = Option::where('type', 'size')->get();
        $this->page['sortOptions'] = Product::$allowedSortingOptions;
        $perPage = $this->property('maxItems');

        if($cat_param == null) {
            $this->page['products'] = Product::active()->listFrontEnd($options, $perPage);
        } else {
            $cat_id = Category::where('slug', $cat_param)->first();
            if($cat_id == null) {
                return \Response::make($this->controller->run('404'), 404);
            }
            $this->page->title = $cat_id->title;
            $this->page->meta_title = $cat_id->title;
            $this->page['products'] = $cat_id->products()->active()->listFrontEnd($options, $perPage);

        }
        $this->page['currentPage'] = $this->page['products']->currentPage();
        $this->page['pages'] = $this->page['products']->lastPage();
        $this->page['perPage'] = $perPage;
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    public function onFilterProduct()
    {
        $this->prepareVars();
    }

}
