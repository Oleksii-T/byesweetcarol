<?php

namespace App\Http\Controllers;

use App\Actions\GetTopTagsAction;
use App\Enums\FeedbackStatus;
use App\Enums\PageStatus;
use App\Models\Author;
use App\Models\Category;
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
        $authors = Author::get();
        $newsCategory = Category::where('slug', 'news')->firstOrFail();

        // Pick top 2 tags by post count (news category only)
        $topNewsTags = GetTopTagsAction::run();

        $col1Tag = $topNewsTags->get(0);
        $col2Tag = $topNewsTags->get(1);

        $newsQ = Post::publised()->latest('published_at')->whereRelation('category', 'slug', 'news');

        $col1Posts = $col1Tag
            ? (clone $newsQ)->whereRelation('tags', 'slug', $col1Tag->slug)->limit(3)->get()
            : collect();

        $col2Posts = $col2Tag
            ? (clone $newsQ)->whereRelation('tags', 'slug', $col2Tag->slug)->whereNotIn('id', $col1Posts->pluck('id'))->limit(3)->get()
            : collect();

        // Column 3: latest news not already shown in col1 or col2
        $shownIds = $col1Posts->merge($col2Posts)->pluck('id');
        $col3Posts = (clone $newsQ)->whereNotIn('id', $shownIds)->limit(3)->get();

        return view('index', compact('page', 'authors', 'newsCategory', 'col1Tag', 'col2Tag', 'col1Posts', 'col2Posts', 'col3Posts'));
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
            $blocks = $page->blocks->sortBy('order');

            return view('contact-us', compact('page', 'blocks'));
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

    public function reviewPolicy()
    {
        $page = Page::get('review-policy');
        $blocks = $page->blocks->sortBy('order');

        return view('page-with-blocks', compact('page', 'blocks'));
    }
}
