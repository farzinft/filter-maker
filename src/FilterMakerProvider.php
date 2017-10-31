<?php

namespace Farzin\FilterMaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;


class FilterMakerProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(TicketFilter::class)->needs(Builder::class)->give(function () {
            return auth()->user()->tickets()->getQuery();
        });


    }

    public function provides()
    {
        return [

        ];
    }
}