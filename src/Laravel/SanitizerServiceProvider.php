<?php

namespace Elegant\Sanitizer\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Nuwave\Lighthouse\Events\RegisterDirectiveNamespaces;

class SanitizerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the sanitizer factory:
        $this->app->singleton('sanitizer', function ($app) {
            return new Factory;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLighthouseAvailable()) {
            $this->bootLighthouse();
        }
    }

    /**
     * Bootstrap Lighthouse related services.
     *
     * @return void
     */
    protected function bootLighthouse()
    {
        Event::listen(RegisterDirectiveNamespaces::class, function () {
            return 'Elegant\Sanitizer\Laravel\Lighthouse';
        });
    }

    /**
     * Determines if the Lighthouse package is installed or not.
     *
     * @return bool
     */
    protected function isLighthouseAvailable()
    {
        return class_exists('Nuwave\Lighthouse\LighthouseServiceProvider');
    }
}
