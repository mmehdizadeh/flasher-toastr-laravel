<?php

namespace Flasher\Toastr\Laravel\ServiceProvider\Providers;

use Flasher\Laravel\ServiceProvider\ResourceManagerHelper;
use Flasher\Prime\Flasher;
use Flasher\Prime\Response\Resource\ResourceManager;
use Flasher\Toastr\Laravel\FlasherToastrServiceProvider;
use Flasher\Toastr\Prime\ToastrFactory;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

class Laravel implements ServiceProviderInterface
{
    /**
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function shouldBeUsed()
    {
        return $this->app instanceof Application;
    }

    public function boot(FlasherToastrServiceProvider $provider)
    {
        $provider->publishes(array(
            flasher_path(__DIR__ . '/../../Resources/config/config.php') => config_path('flasher_toastr.php'),
        ), 'flasher-config');
    }

    public function register(FlasherToastrServiceProvider $provider)
    {
        $provider->mergeConfigFrom(flasher_path(__DIR__ . '/../../Resources/config/config.php'), 'flasher_toastr');
        $this->appendToFlasherConfig();

        $this->registerServices();
    }

    public function registerServices()
    {
        $this->app->singleton('flasher.toastr', function (Container $app) {
            return new ToastrFactory($app['flasher.storage_manager']);
        });

        $this->app->alias('flasher.toastr', 'Flasher\Toastr\Prime\ToastrFactory');

        $this->app->extend('flasher', function (Flasher $flasher, Container $app) {
            $flasher->addFactory('toastr', $app['flasher.toastr']);

            return $flasher;
        });

        $this->app->extend('flasher.resource_manager', function (ResourceManager $resourceManager) {
            ResourceManagerHelper::process($resourceManager, 'toastr');

            return $resourceManager;
        });
    }

    public function appendToFlasherConfig()
    {
        $flasherConfig = $this->app['config']->get('flasher.adapters.toastr', array());

        $toastrConfig = $this->app['config']->get('flasher_toastr', array());

        $this->app['config']->set('flasher.adapters.toastr', array_merge($toastrConfig, $flasherConfig));
    }
}
