<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeleteOldPostsCommandTest extends TestCase
{
    public function createApplication()
    {
        Facade::setFacadeApplication(null);

        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            // Console tests do not need the HTTP routes, which query redirects during boot.
            $app['config']->set('app.providers', array_values(array_diff(
                $app['config']->get('app.providers'),
                [RouteServiceProvider::class],
            )));
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.database', ':memory:');
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_it_soft_deletes_only_posts_created_strictly_before_the_date(): void
    {
        $old = $this->insertPost('2026-01-31 23:59:59');
        $atCutoff = $this->insertPost('2026-02-01 00:00:00');
        $new = $this->insertPost('2026-02-02 12:00:00');
        $alreadyDeleted = $this->insertPost('2026-01-01 12:00:00', '2026-01-15 12:00:00');
        $deletedIds = [];

        Post::deleted(function (Post $post) use (&$deletedIds) {
            $deletedIds[] = $post->id;
        });

        $this->artisan('posts:delete-older-than', ['date' => '2026-02-01'])
            ->expectsOutput('Soft deleted 1 posts created before 2026-02-01.')
            ->assertSuccessful();

        $this->assertSoftDeleted('posts', ['id' => $old]);
        $this->assertDatabaseHas('posts', ['id' => $atCutoff, 'deleted_at' => null]);
        $this->assertDatabaseHas('posts', ['id' => $new, 'deleted_at' => null]);
        $this->assertDatabaseHas('posts', [
            'id' => $alreadyDeleted,
            'deleted_at' => '2026-01-15 12:00:00',
        ]);
        $this->assertDatabaseCount('posts', 4);
        $this->assertSame([$old], $deletedIds);

        $this->artisan('posts:delete-older-than', ['date' => '2026-02-01'])
            ->expectsOutput('Soft deleted 0 posts created before 2026-02-01.')
            ->assertSuccessful();
    }

    public function test_dry_run_counts_matches_without_deleting_posts(): void
    {
        $old = $this->insertPost('2026-01-31 23:59:59');
        $this->insertPost('2026-02-01 00:00:00');
        $this->insertPost('2026-01-01 00:00:00', '2026-01-15 12:00:00');

        $this->artisan('posts:delete-older-than', ['date' => '2026-02-01', '--dry-run' => true])
            ->expectsOutput('Would soft delete 1 posts created before 2026-02-01.')
            ->assertSuccessful();

        $this->assertDatabaseHas('posts', ['id' => $old, 'deleted_at' => null]);
        $this->assertSame(2, Post::count());
        $this->assertDatabaseCount('posts', 3);
    }

    public function test_it_deletes_all_matching_posts_across_batches(): void
    {
        DB::table('posts')->insert(array_fill(0, 501, [
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => '2026-01-01 00:00:00',
        ]));

        $this->artisan('posts:delete-older-than', ['date' => '2026-02-01'])
            ->expectsOutput('Soft deleted 501 posts created before 2026-02-01.')
            ->assertSuccessful();

        $this->assertSame(0, Post::count());
        $this->assertSame(501, Post::onlyTrashed()->count());
        $this->assertDatabaseCount('posts', 501);
    }

    /**
     * @dataProvider invalidDates
     */
    public function test_it_rejects_invalid_dates_without_deleting_posts(string $date): void
    {
        $old = $this->insertPost('2020-01-01 00:00:00');

        $this->artisan('posts:delete-older-than', ['date' => $date])
            ->expectsOutput('The date must be a valid date in YYYY-MM-DD format.')
            ->assertFailed();

        $this->assertDatabaseHas('posts', ['id' => $old, 'deleted_at' => null]);
    }

    public static function invalidDates(): array
    {
        return [
            ['not-a-date'],
            ['2026-02-30'],
            ['2026-13-01'],
            ['02/01/2026'],
            ['2026-02-01 12:00:00'],
            [''],
        ];
    }

    private function insertPost(string $createdAt, ?string $deletedAt = null): int
    {
        return DB::table('posts')->insertGetId([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);
    }
}
