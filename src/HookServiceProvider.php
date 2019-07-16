<?php

namespace Hongyukeji\Hook;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * Registers the hook singleton.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hook', function ($app) {
            return new Hook();
        });
    }
}
