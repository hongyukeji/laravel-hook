<?php

namespace Hongyukeji\Hook;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HookBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Adds a directive in Blade for actions
         */
        Blade::directive('action', function ($expression) {
            return "<?php Hook::action({$expression}); ?>";
        });

        /*
         * Adds a directive in Blade for filters
         */
        Blade::directive('filter', function ($expression) {
            return "<?php echo Hook::filter({$expression}); ?>";
        });
    }
}
