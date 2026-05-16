<?php

namespace App\Traits;

use App\Models\User;

/**
 * Controller for developers use only
 * To track execution time use $this-t()
 * To dump values use $this->d
 * Set up _authorize() to you needs
 * See example() as an example of usage
 */
trait DevTrait
{
    private $d = [];

    private $timings = [];

    private $queryLog = false;

    private $user;

    private $fullStart;

    private $start;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->fullStart = $this->start = microtime(true);
    }

    // base method to navigate and manage testing logic
    public function action($slug)
    {
        if (! method_exists($this, $slug)) {
            dd('ERROR: action not found'); //? 404 instead
        }

        if ($slug != 'public') {
            $this->_authorize();
        }

        $result = $this->{$slug}();

        // dump query log if is set
        if ($this->queryLog) {
            dump('QUERY LOG', \DB::getQueryLog());
        }

        // dump $timings only if we set some
        if ($this->timings) {
            $this->timings['timings-finish'] = microtime(true) - $this->fullStart;
            dump('TIMINGS', $this->timings);
        }

        // dump $d only if we set some
        if ($this->d) {
            dump('RESULT DUMP', $this->d);
        }

        return $result;
    }

    private function log()
    {
        dlog('DevController@log: ', request()->all()); //! LOG
    }

    // login to user by ID (login to admin by default)
    private function login()
    {
        $user = request()->user;

        if (! $user) {
            $user = User::whereIn('email', ['admin@mail.com', 'admin@admin.com'])->first();
            if (! $user) {
                // todo add belongsTo relation check
                $user = User::whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })->first();
            }
            if (! $user) {
                dump('Admin user not found. Please provide user_id manualy');
                dd(User::all());
            }
        } else {
            $user = User::find($user);
        }

        auth()->login($user);

        return redirect('/');
    }

    // get phpinfo
    private function phpinfo()
    {
        phpinfo();
    }

    // get client IP
    private function ip()
    {
        $ip = request()->ip();

        return "Your ip is: $ip";
    }

    // helper to store execution time
    private function t($key = null)
    {
        $t = microtime(true) - $this->start;

        if ($key) {
            $this->timings[$key] = $t;
        } else {
            $this->timings[] = $t;
        }

        $this->start = microtime(true);
    }

    private function d($value, $key = null)
    {
        if ($key) {
            $this->d[$key] = $value;
        } else {
            $this->d[] = $value;
        }
    }

    // reset start time
    private function setFullStart($key = null)
    {
        $this->fullStart = $this->start = microtime(true);
    }

    // authorize access to methods
    private function _authorize()
    {
        $ok = true || isdev() || $this->user?->isAdmin();

        abort_if(! $ok, 403);
    }

    // enable query log
    private function enableQueryLog()
    {
        $this->queryLog = true;
        \DB::connection()->enableQueryLog();
    }
}
