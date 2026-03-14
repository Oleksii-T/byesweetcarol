<?php

namespace App\Http\Controllers;

use App\Enums\FeedbackStatus;
use App\Enums\PageStatus;
use App\Models\Author;
use App\Models\Feedback;
use App\Models\FeedbackBan;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $page = Page::get('/');
        $q = Post::publised()->latest('published_at');
        $latestReviews = (clone $q)->whereRelation('category', 'slug', 'reviews')->limit(3)->get();
        $latestGuides = (clone $q)->whereRelation('category', 'slug', 'guides')->limit(2)->get();
        $latestLists = (clone $q)->whereRelation('category', 'slug', 'lists')->limit(2)->get();
        $latestNews = (clone $q)->whereRelation('category', 'slug', 'news')->limit(4)->get();
        $authors = Author::get();

        return view('index', compact('page', 'authors', 'latestReviews', 'latestGuides', 'latestNews', 'latestLists'));
    }

    public function show(Request $request)
    {
        $page = Page::query()
            ->where('link', \Request::path())
            ->where('status', PageStatus::PUBLISHED)
            ->firstOrFail();

        return view('page', compact('page'));
    }

    public function contactUs(Request $request)
    {
        if (! $request->ajax()) {
            $page = Page::get('contact');

            return view('contact-us', compact('page'));
        }

        $input = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'text' => ['required', 'string', 'max:2000'],
        ]);

        $user = auth()->user();
        $ban = $user ? FeedbackBan::where('type', 'user')->where('value', $user->id)->first() : null;
        $ban ??= FeedbackBan::where('type', 'ip')->where('value', $request->ip())->first();
        $ban ??= FeedbackBan::where('type', 'name')->where('value', $input['name'])->first();
        $ban ??= FeedbackBan::where('type', 'email')->where('value', $input['email'])->first();

        if ($ban && $ban->is_active) {
            // activity('feedback-bans')
            //     ->event('catch')
            //     ->withProperties(infoForActivityLog())
            //     ->on($ban)
            //     ->log('');

            if ($ban->action == 'abort') {
                abort(429);
            } elseif ($ban->action == 'spam') {
                $input['status'] = FeedbackStatus::SPAM;
            }
        }

        $input['user_id'] = $user->id ?? null;
        $input['ip'] = $request->ip();

        Feedback::create($input);

        return $this->jsonSuccess('Message send');
    }

    public function privacy()
    {
        $page = Page::get('privacy-policy');
        $blocks = $page->blocks->sortBy('order');

        return view('page-with-blocks', compact('page', 'blocks'));
    }

    public function terms()
    {
        $page = Page::get('terms-of-use');
        $blocks = $page->blocks->sortBy('order');

        return view('page-with-blocks', compact('page', 'blocks'));
    }

    public function aboutUs()
    {
        $page = Page::get('about-us');
        $blocks = $page->blocks->sortBy('order');

        return view('page-with-blocks', compact('page', 'blocks'));
    }

    public function cookiePolicy()
    {
        $page = Page::get('cookie-policy');
        $blocks = $page->blocks->sortBy('order');

        return view('page-with-blocks', compact('page', 'blocks'));
    }
}
