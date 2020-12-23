<?php namespace Acme\Setting;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Acme\Setting\Components\SlideList' => 'slidelist',
            'Acme\Setting\Components\AdvantageWidget' => 'advantagewidget',
            'Acme\Setting\Components\Faq' => 'faq',
        ];
    }
    public function registerSettings()
    {

    }
}
