<?php
declare(strict_types=1);

namespace GorillaDash\OutlookFormatter;

use Illuminate\Support\ServiceProvider;

class FormatterServiceProvider extends ServiceProvider
{
    /**
     * Config path
     * @var string
     */
    protected $configPath = __DIR__ . '/../config/outlook-formatter.php';


    /**
     * Perform post-registration booting of services.
     *
     * @throws \InvalidArgumentException
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath => $this->app->configPath() . '/outlook-formatter.php',
        ], 'config');
    }

    /**
     * Register service
     *
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath, 'outlook-formatter');

        $this->app->singleton('OutlookFormatter', function ($app) {
            $formatter = new Formatter($this->app['config']->get('outlook-formatter.maxWidth'));
            $formatter->setAutoCenter($this->app['config']->get('outlook-formatter.autoCenter'));
            return $formatter;
        });
    }
}
