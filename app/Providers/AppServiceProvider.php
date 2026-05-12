<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // @activeRoute('admin.dashboard') → returns 'active' if current route matches
        Blade::directive('activeRoute', function ($expression) {
            return "<?php echo request()->routeIs($expression) ? 'active' : ''; ?>";
        });
        
         Paginator::useBootstrapFive(); // Or useBootstrapFour()
    }
}
