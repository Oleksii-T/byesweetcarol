<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Translators\DeeplTranslator;
use Illuminate\Support\Facades\Mail;

/**
 * Controller for developers use only
 * To track execution time use $this-t()
 * To dump values use $this->d
 * Set up _authorize() to you needs
 * See example() as an example of usage
 */
class DevController extends Controller
{
    use \App\Traits\DevTrait;
    use \App\Traits\LogsActivityBasic;

    private function test()
    {
        $d = [];

        dd($d);
    }

    private function fixRichTextLinks()
    {
        $d = [];
        // $old = 'https://hermit.rigmanagers.com/';
        $old = 'https://www.hermitgamer.com//';
        $new = 'https://www.hermitgamer.com/';

        foreach (\App\Models\BlockItem::all() as $block) {
            $value = $block->getRawOriginal('value');

            if (! str_contains($value, $old)) {
                continue;
            }

            $value = str_replace($old, $new, $value);
            $block->value = $value;
            $block->save();
            $d[] = $block->toArray();
        }

        dd($d);
    }

    private function checkDeprecatedAttchmentables()
    {
        $d = [];

        \App\Models\Attachmentable::whereNull('attachmentable_type')->delete();

        $as = \App\Models\Attachmentable::all();

        foreach ($as as $a) {
            $class = '\\'.$a->attachmentable_type;
            $model = $class::find($a->attachmentable_id);

            if (! $model) {
                $d[] = $a->toArray();
                $a->delete();
            }
        }

        dd($d);
    }

    private function generateSitemap()
    {
        \App\Actions\GenerateSitemap::run();
    }

    private function example()
    {
        $this->enableQueryLog();

        $this->d('creating 1000 els array and collection...');

        $array = range(-500, 500);
        shuffle($array);

        $colleciton = collect($array);

        $this->d('starting sorting...');
        $this->setFullStart();

        sort($array);

        $this->t('array_sort');

        $colleciton->sort();

        $this->t('collection_sort');
        $this->d('sorting done.');

        return $array;
    }

    // dummy public method.
    // can be used to showcase some functionality to external user.
    private function public()
    {
        return 'Hello from devs!';
    }

    // test emails
    private function emails()
    {
        $t = request()->type;
        $email = request()->email;

        // other emails test here...

        if (! isset($mail)) {
            dd('ERROR: mail not found');
        }

        if ($email) {
            Mail::to($email)->send($mail);
        }

        return $mail;
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

    private function translator()
    {
        $s = resolve(DeeplTranslator::class);
        dd($s->make('Я хочу больше работы сегодня.', 'en'));
    }

    // get phpinfo
    private function phpinfo()
    {
        phpinfo();
    }
}
