<?php

namespace PivoAndCode\WordpressMeilisearch\Providers;

use Illuminate\Support\ServiceProvider;
use PivoAndCode\WordpressMeilisearch\WordpressMeilisearch;

require_once __DIR__ . '/../../vendor/autoload.php';

class WordpressMeilisearchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('WordpressMeilisearch', function () {
            return new WordpressMeilisearch($this->app);
        });

        $this->app->singleton(\Meilisearch\Client::class, function() {
            return new \Meilisearch\Client(
                config('meilisearch.host'),
                config('meilisearch.key')
            );
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/meilisearch.php',
            'meilisearch'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/meilisearch.php' => $this->app->configPath('meilisearch.php'),
        ], 'config');

        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'WordpressMeilisearch',
        );

        $this->loadRoutesFrom(
            __DIR__ . '/../../routes/web.php'
        );

        $this->app->make('WordpressMeilisearch');
    }
}
