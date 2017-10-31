<?php

namespace Farzin\FilterMaker;

use Illuminate\Support\ServiceProvider;
use Artisan;

class FilterMakerProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([FilterMaker::class]);

        $this->publishes([
            __DIR__. DIRECTORY_SEPARATOR . '../filter-maker.php' => config_path('filter-maker.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }


}