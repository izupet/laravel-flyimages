<?php

namespace Izupet\FlyImages;

use Illuminate\Support\ServiceProvider;

class FlyImagesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/flyimages.php' =>  config_path('flyimages.php'),
        ]);
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
    }
}
