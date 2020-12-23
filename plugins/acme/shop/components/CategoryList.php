<?php namespace Acme\Shop\Components;

use Acme\Shop\Models\Category;

class CategoryList extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
      return [
          'name' => 'Список категорий',
          'description' => 'Показать категории'
      ];
    }

    public function onRun()
    {
      $this->page['categories'] = Category::orderBy('sort_order', 'asc')->get();
    }
}
