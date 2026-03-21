<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\PageBlock;
use App\Models\Tag;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // \Debugbar::disable();
        if (isdev()) {
            \Debugbar::enable();
        }

        \View::composer('*', function ($view) {
            $cashTime = 5;
            $user = auth()->user();

            $view->with('currentUser', $user);

            $view->with(
                'headerCategories',
                Cache::remember('headerCategories', $cashTime, function () {
                    return Category::where('in_menu', true)->orderBy('order')->get();
                })
            );

            $data = [
                'csrf' => csrf_token(),
                'route_name' => \Route::currentRouteName(),
                'translations' => [],
                'recaptcha_key' => config('services.recaptcha.public_key'),
            ];
            $translationsForJs = [
            ];
            foreach ($translationsForJs as $t) {
                $data['translations'][str_replace('.', '_', $t)] = trans($t);
            }
            if ($user) {
                $data['user'] = [
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }
            $view->with('LaravelDataForJS', json_encode($data));

            $header = Cache::remember('header', 60 * 10, fn () => PageBlock::whereRelation('page', 'link', '/')->where('name', 'header')->first());
            $view->with('header', $header);

            $footer = Cache::remember('footer', 60 * 10, fn () => PageBlock::whereRelation('page', 'link', '/')->where('name', 'footer')->first());
            $view->with('footer', $footer);

            $view->with(
                'topTags',
                Cache::remember('topTags', $cashTime, function () {
                    return Tag::withCount('posts')->orderByDesc('posts_count')->limit(20)->get();
                })
            );
        });

        \Blade::directive('admin', function ($arguments) {
            return '<?php if(auth()->user()?->isAdmin()): ?>';
        });

        \Blade::directive('endadmin', function ($arguments) {
            return '<?php endif; ?>';
        });

        \Blade::directive('svg', function ($arguments) {
            // Funky madness to accept multiple arguments into the directive
            [$path, $class] = array_pad(explode(',', trim($arguments, '() ')), 2, '');
            $path = trim($path, "' ");
            $class = trim($class, "' ");

            // Create the dom document as per the other answers
            $svg = new \DOMDocument();
            $svg->load(public_path($path));
            $svg->documentElement->setAttribute('class', $class);
            $output = $svg->saveXML($svg->documentElement);

            return $output;
        });

        // add dynamic values to adminlte menu
        $adminlteMenus = config('adminlte.menu');
        foreach ($adminlteMenus as &$menu) {
            $route = $menu['route'] ?? null;
            if ($route == 'admin.games.index') {
                try {
                    $pending = \App\Models\Game::query()
                        ->where('status', \App\Enums\GameStatus::CALENDAR_DRAFT)
                        ->count();
                    if ($pending) {
                        $menu['label'] = $pending;
                    }
                } catch (\Throwable $th) {
                }
            }
            if ($route == 'admin.comments.index') {
                try {
                    $pending = \App\Models\Comment::query()
                        ->where('status', \App\Enums\CommentStatus::PENDING)
                        ->count();
                    if ($pending) {
                        $menu['label'] = $pending;
                    }
                } catch (\Throwable $th) {
                }
            }
            if ($route == 'admin.feedbacks.index') {
                try {
                    $pending = \App\Models\Feedback::query()
                        ->where('status', \App\Enums\FeedbackStatus::PENDING)
                        ->count();
                    if ($pending) {
                        $menu['label'] = $pending;
                    }
                } catch (\Throwable $th) {
                }
            }
        }
        config(['adminlte.menu' => $adminlteMenus]);

        Paginator::defaultView('vendor.pagination.bootstrap-5');

        Carbon::mixin(new class
        {
            public function adminFormat()
            {
                return function () {
                    return $this->format('d/m/Y H:i');
                };
            }
        });
    }
}
