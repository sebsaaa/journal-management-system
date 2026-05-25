<?php namespace Seba\Punktoza;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Punktoza',
            'description' => 'System zarzadzania wykazem czasopism',
            'author'      => 'Seba',
            'icon'        => 'icon-graduation-cap'
        ];
    }

    public function registerComponents()
    {
        return [
            \Seba\Punktoza\Components\JournalManager::class => 'journalManager'
        ];
    }
}