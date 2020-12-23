<?php namespace Acme\Setting\Components;

use Acme\Setting\Models\Advantage;

class AdvantageWidget extends \Cms\Classes\ComponentBase
{
    public $advantage;

    public function componentDetails()
    {
        return [
            'name' => 'Преимущества',
            'description' => 'Отобразить преимущества на сайте'
        ];
    }

    public function defineProperties()
    {
        return [
            'advantageName' => [
                'title'             => 'Выбор блока',
                'default'           => 1,
                'type'              => 'dropdown',
                'placeholder'       => 'Выберите',
            ]
        ];
    }

    public function getAdvantageNameOptions()
    {
      return Advantage::all()->lists('title', 'id');
    }

    public function onRun()
    {
      $query = Advantage::where( 'id', '=', $this->property('advantageName') )->first();
      $this->advantage = $query;
    }
}