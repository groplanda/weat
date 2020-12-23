<?php namespace Acme\Setting\Components;

use Acme\Setting\Models\Slider;

class SlideList extends \Cms\Classes\ComponentBase
{
    public $slides;

    public function componentDetails()
    {
        return [
            'name' => 'Слайдер',
            'description' => 'Список слайдов'
        ];
    }

    public function onRun()
    {
      $query = Slider::orderBy('sort_order', 'asc')->get();
      $this->slides = $query;
    }

}