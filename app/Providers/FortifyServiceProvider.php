<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // login config
        Fortify::loginView(function () {
            $page = \App\Models\Page::get('login');
            if (auth()->user()) {
                return redirect()->route('admin.index');
            }

            return view('auth.login', compact('page'));
        });
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            dd('rate limit');

            return Limit::perMinute(50)->by($email.$request->ip());
        });

        // register config
        Fortify::registerView(function () {
            $page = \App\Models\Page::get('register');

            return view('auth.register', compact('page'));
        });
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request)
            {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'redirect' => route('index'),
                    ],
                    'message' => 'Registered successfully',
                ]);
            }
        });

        // reset pass config
        Fortify::requestPasswordResetLinkView(function () {
            $page = \App\Models\Page::get('forgot-password');

            return view('auth.password-email', compact('page'));
        });
        Fortify::resetPasswordView(function ($request) {
            $token = $request->token;
            $email = $request->email;

            return view('auth.password-reset', compact('token', 'email'));
        });
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
    }
}
