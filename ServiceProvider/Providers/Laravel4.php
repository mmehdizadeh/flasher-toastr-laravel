<?php

namespace Flasher\Toastr\Laravel\ServiceProvider\Providers;

use Flasher\Toastr\Laravel\FlasherToastrServiceProvider;
use Illuminate\Foundation\Application;

final class Laravel4 extends Laravel
{
    public function shouldBeUsed()
    {
        return $this->app instanceof Application && 0 === strpos(Application::VERSION, '4.');
    }

    public function boot(FlasherToastrServiceProvider $provider)
    {
        $provider->package(
            'php-flasher/flasher-toastr-laravel',
            'flasher_toastr',
            flasher_path(__DIR__ . '/../../Resources')
        );
        $this->appendToFlasherConfig();
    }

    public function register(FlasherToastrServiceProvider $provider)
    {
        $this->registerServices();
    }

    public function appendToFlasherConfig()
    {
        $flasherConfig = $this->app['config']->get('flasher::config.adapters.toastr', array());

        $toastrConfig = $this->app['config']->get('flasher_toastr::config', array());

        $this->app['config']->set('flasher::config.adapters.toastr', array_merge($toastrConfig, $flasherConfig));
    }
}
