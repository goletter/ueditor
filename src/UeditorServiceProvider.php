<?php

namespace Yanthink\Ueditor;

use Illuminate\Support\ServiceProvider;

class UeditorServiceProvider extends ServiceProvider
{

    protected $defer = true;

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/ueditor.php' => config_path('ueditor.php')], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/ueditor.php', 'ueditor');
    }

    public function register()
    {
        $this->app->bind('ueditor', function ($app) {
            return new Ueditor($app['request'], $app['validator'], $app['filesystem'], $app['config']->get('ueditor'));
        });

        $this->app->alias('ueditor', Contracts\Ueditor::class);
    }

    public function provides()
    {
        return ['ueditor', Contracts\Ueditor::class];
    }

}
