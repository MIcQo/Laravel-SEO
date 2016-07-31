<?php

namespace MichalKoval\SEO;

use Illuminate\Support\ServiceProvider;

class SEOServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSeoBuilder();

        $this->app->alias('seo', 'MichalKoval\SEO\SEOBuilder');
    }

    /**
     * Register the Seo builder instance.
     *
     * @return void
     */
    protected function registerSeoBuilder()
    {
        $this->app->singleton('seo', function ($app) {
            return new SEOBuilder($app['url'], $app['view']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['seo', 'MichalKoval\SEO\SEOBuilder'];
    }
}
