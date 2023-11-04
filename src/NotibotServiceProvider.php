<?php

namespace W3Devmaster\Notibot;

use Illuminate\Support\ServiceProvider;

class NotibotServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerPublishables();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notibot.php', 'notibot');

        $this->app->singleton(Notibot::class,function(){
            return new Notibot();
        });
    }

    protected function registerPublishables(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/notibot.php' => config_path('notibot.php'),
        ], 'config');
    }

}
