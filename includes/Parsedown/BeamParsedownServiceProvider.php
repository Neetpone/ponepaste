<?php

namespace ArdiSSoebrata\BeamParsedown;

use Illuminate\Support\ServiceProvider;

class BeamParsedownServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', ':lc:vendor');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', ':lc:vendor');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/beam-parsedown.php', 'beam-parsedown');

        // Register the service the package provides.
        $this->app->singleton('beam-parsedown', function ($app) {
            $parse = new BeamParsedown();
            
            // Set from config.
            $parse->setBreaksEnabled(config('beam-parsedown.breaks_enabled', false));
            $parse->setMarkupEscaped(config('beam-parsedown.markup_escaped', false));
            $parse->setUrlsLinked(config('beam-parsedown.urls_linked', true));
            $parse->setSafeMode(config('beam-parsedown.safe_mode', false));

            return $parse;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        // @codeCoverageIgnoreStart
        return ['beam-parsedown'];
        // @codeCoverageIgnoreEnd
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/beam-parsedown.php' => config_path('beam-parsedown.php'),
        ], 'beam-parsedown.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/:lc:vendor'),
        ], 'beam-parsedown.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/:lc:vendor'),
        ], 'beam-parsedown.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/:lc:vendor'),
        ], 'beam-parsedown.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}