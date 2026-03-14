<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::bind('game', function ($value) {
            return \App\Models\Game::where('slug', $value)->first();
        });

        Route::bind('post', function ($value) {
            return \App\Models\Post::where('slug', $value)->first();
        });

        Route::bind('category', function ($value) {
            return \App\Models\Category::where('slug', $value)->first();
        });

        Route::bind('tag', function ($value) {
            return \App\Models\Tag::where('slug', $value)->first();
        });

        Route::bind('author', function ($value) {
            $a = \App\Models\Author::where('slug', $value)->first();

            // if (!$a) {
            //     dd($value, $a);
            // } else {
            //     dd($a);
            // }
            return $a;
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['web'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
