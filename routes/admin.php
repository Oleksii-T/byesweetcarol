<?php

use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeedbackBanController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TranslateController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
 *
 * Routes for admin panel
 *
 */

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('admin.index');
    }

    return view('admin.auth.login');
})->name('login');

Route::middleware('is-admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::view('icons', 'admin.icons');

    Route::resource('users', UserController::class)->except('show');

    Route::resource('categories', CategoryController::class)->except('show');

    Route::resource('tags', TagController::class)->except('show');
    Route::put('tags/{tag}/transfer', [TagController::class, 'transferPosts'])->name('tags.transfer');

    Route::post('games/{game}/scrape', [GameController::class, 'scrape'])->name('games.scrape');
    Route::resource('games', GameController::class)->except('show');

    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('{author}/edit/socials', [AuthorController::class, 'socials'])->name('socials');
        Route::get('{author}/edit/blocks', [AuthorController::class, 'blocks'])->name('blocks');
        Route::put('{author}/edit/socials', [AuthorController::class, 'updateSocials'])->name('update-socials');
        Route::post('{author}/edit/blocks', [AuthorController::class, 'updateBlocks'])->name('update-blocks');
    });
    Route::resource('authors', AuthorController::class)->except('show');

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::put('{comment}/status', [CommentController::class, 'updateStatus'])->name('update-status');
    });
    Route::resource('comments', CommentController::class)->except('show');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::post('feedback-bans/{feedback-ban}/toggle', [FeedbackBanController::class, 'toggle'])->name('feedback-bans.toggle');
    Route::resource('feedback-bans', FeedbackBanController::class)->only('index', 'store', 'update', 'destroy');

    Route::post('feedback/{feedback}/spam-by-ip', [FeedbackController::class, 'spamByIp'])->name('feedbacks.spam-by-ip');
    Route::resource('feedbacks', FeedbackController::class)->only('show', 'index', 'destroy', 'update');

    Route::resource('platforms', PlatformController::class)->except('show', 'create', 'edit');

    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('{page}/blocks', [PageController::class, 'blocks'])->name('blocks');
        Route::get('{page}/template', [PageController::class, 'template'])->name('template');
        Route::put('{page}/update-template', [PageController::class, 'updateTemplate'])->name('update-template');
        Route::post('{page}/blocks', [PageController::class, 'updateBlocks'])->name('update-blocks');
    });
    Route::resource('pages', PageController::class)->except('show');

    Route::resource('redirects', RedirectController::class)->except('show', 'edit', 'create');

    Route::view('docs', 'admin.docs')->name('docs');
});

Route::middleware('is-writer')->group(function () {
    Route::post('translate', [TranslateController::class, 'translate'])->name('translate');

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('create', [PostController::class, 'create'])->name('create');
        Route::get('create/ai', [PostController::class, 'createAi'])->name('create-ai');
        Route::get('{post}/blocks', [PostController::class, 'blocks'])->can('update', 'post')->name('blocks');
        Route::get('{post}/faqs', [PostController::class, 'faqs'])->can('update', 'post')->name('faqs');
        Route::get('{post}/assets', [PostController::class, 'assets'])->can('update', 'post')->name('assets');
        Route::get('{post}/related', [PostController::class, 'related'])->can('update', 'post')->name('related');
        Route::get('{post}/conclusion', [PostController::class, 'conclusion'])->can('update', 'post')->name('conclusion');
        Route::put('{post}/conclusion', [PostController::class, 'updateConclusion'])->can('update', 'post')->name('update-conclusion');
        Route::get('{post}/reviewsFields', [PostController::class, 'reviewsFields'])->can('update', 'post')->name('reviewsFields');
        Route::get('{id}/recover', [PostController::class, 'recover'])->name('recover');
        Route::get('{post}/edit', [PostController::class, 'edit'])->can('update', 'post')->name('edit');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('ai-generate', [PostController::class, 'aiGenerate'])->name('ai-generate');
        Route::post('ai-store', [PostController::class, 'aiStore'])->name('ai-store');
        Route::post('{post}/blocks', [PostController::class, 'updateBlocks'])->can('update', 'post')->name('update-blocks');
        Route::post('{post}/faqs', [PostController::class, 'storeFaq'])->can('update', 'post')->name('store-faq');
        Route::put('{post}/assets', [PostController::class, 'updateAssets'])->can('update', 'post')->name('update-assets');
        Route::put('{post}/reviewsFields', [PostController::class, 'updateReviewsFields'])->can('update', 'post')->name('update-reviewsFields');
        Route::put('{post}/related', [PostController::class, 'updateRelated'])->can('update', 'post')->name('update-related');
        Route::put('{post}/faqs/{faq}', [PostController::class, 'updateFaq'])->can('update', 'post')->name('update-faq');
        Route::put('{post}', [PostController::class, 'update'])->can('update', 'post')->name('update');
        Route::delete('{post}/faqs/{faq}', [PostController::class, 'destroyFaq'])->can('update', 'post')->name('destroy-faq');
        Route::delete('{post}', [PostController::class, 'destroy'])->can('update', 'post')->name('destroy');
    });

    Route::prefix('attachments')->name('attachments.')->group(function () {
        Route::get('images', [AttachmentController::class, 'images'])->name('images');
        Route::get('{attachment}/download', [AttachmentController::class, 'download'])->name('download');
        Route::post('upload', [AttachmentController::class, 'upload'])->name('upload');
    });
    Route::resource('attachments', AttachmentController::class)->except('create', 'store', 'show');
});
