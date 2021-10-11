<?php

namespace JaxWilko\MediaStream;

use System\Classes\PluginBase;
use Backend;
use App;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'          => 'MediaStream',
            'description'   => 'Adds a streamable component',
            'author'        => 'Jack Wilkinson',
            'icon'          => 'icon-file-video-o',
        ];
    }

    public function registerComponents()
    {
        return [
            \JaxWilko\MediaStream\Components\Video::class => 'video'
        ];
    }

    public function registerNavigation()
    {
        return [
            'mediastream' => [
                'label'       => 'MediaStream Meta',
                'url'         => Backend::url('jaxwilko/mediastream/metadata'),
                'icon'        => 'icon-pencil-square-o',
                'permissions' => ['jaxwilko.mediastream.*'],
                'order'       => 500,
            ]
        ];
    }
}
