<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Author;
use App\Models\BlockItem;
use App\Models\ContentBlock;
use App\Models\Game;
use App\Models\Page;
use App\Models\Post;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class CleanTrashedPostImagesTest extends TestCase
{
    public function createApplication()
    {
        Facade::setFacadeApplication(null);
        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            // HTTP routes query redirects during boot and are not needed by this command.
            $app['config']->set('app.providers', array_values(array_diff(
                $app['config']->get('app.providers'),
                [RouteServiceProvider::class],
            )));
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.database', ':memory:');
            $app['config']->set('database.connections.sqlite.url', null);
            $app['config']->set('cache.default', 'array');
            $app['config']->set('activitylog.enabled', false);
        });
        $app->make(Kernel::class)->bootstrap();

        Schema::create('posts', function ($table) {
            $table->id();
            $table->text('intro')->nullable();
            $table->text('conclusion')->nullable();
            $table->softDeletes();
        });
        Schema::create('pages', function ($table) {
            $table->id();
            $table->text('content')->nullable();
        });
        foreach (['authors' => ['description', 'description_small'], 'categories' => ['description'], 'games' => ['description', 'summary']] as $tableName => $columns) {
            Schema::create($tableName, function ($table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->text($column)->nullable();
                }
            });
        }
        foreach ([
            '2022_08_15_071427_create_attachments_table.php',
            '2022_08_17_101615_create_post_blocks_table.php',
            '2022_08_17_114447_create_block_items_table.php',
            '2024_02_22_184110_create_faqs_table.php',
            '2022_08_03_192042_create_page_blocks_table.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        (require database_path('migrations/2024_05_17_113841_create_attachmentables_table.php'))->up();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('aimages');
        Storage::fake('adocuments');
    }

    public function test_deletes_only_images_exclusive_to_trashed_posts_across_batches(): void
    {
        $trashed = $this->createPost(true);
        $otherTrashed = $this->createPost(true);
        $active = $this->createPost(false);
        $exclusive = $this->image('exclusive.jpg', $trashed);
        $onlyTrash = $this->image('two-trashed.jpg', $trashed);
        $this->attach($onlyTrash, $otherTrashed);
        $shared = $this->image('shared.jpg', $trashed);
        $this->attach($shared, $active);
        $blockImage = $this->image('block.jpg');
        $this->attach($blockImage, $this->blockItem($trashed), BlockItem::class);
        $blockShared = $this->image('block-shared.jpg', $trashed);
        $this->attach($blockShared, $this->blockItem($active), BlockItem::class);
        $otherOwner = $this->image('game.jpg', $trashed);
        $this->attach($otherOwner, 123, Game::class);
        $unknownOwner = $this->image('unknown.jpg', $trashed);
        $this->attach($unknownOwner, null, null);
        $unrelated = $this->image('unrelated.jpg');
        $activeOnly = $this->image('active-only.jpg', $active);
        $document = $this->image('document.pdf', $trashed, 'document');

        $this->artisan('posts:clean-images', ['--batch-size' => 2])
            ->expectsOutput('Deleted 3 images; preserved 4 shared images; 0 failed.')
            ->assertSuccessful();

        foreach ([$exclusive, $onlyTrash, $blockImage] as $image) {
            $this->assertDatabaseMissing('attachments', ['id' => $image->id]);
            $this->assertDatabaseMissing('attachmentables', ['attachment_id' => $image->id]);
            Storage::disk('aimages')->assertMissing($image->name);
        }
        foreach ([$shared, $blockShared, $otherOwner, $unknownOwner, $unrelated, $activeOnly, $document] as $image) {
            $this->assertDatabaseHas('attachments', ['id' => $image->id]);
            Storage::disk(Attachment::disk($image->type))->assertExists($image->name);
        }
        $this->assertDatabaseCount('posts', 3);
    }

    public function test_rich_text_urls_protect_shared_images_and_identify_inline_only_images(): void
    {
        $trashed = $this->createPost(true);
        $active = $this->createPost(false);
        $intro = $this->image('intro.jpg', $trashed);
        $block = $this->image('block space.jpg', $trashed);
        $page = $this->image('page.jpg', $trashed);
        $faq = $this->image('faq.jpg', $trashed);
        $inlineOnly = $this->image('inline-only.jpg');
        DB::table('posts')->where('id', $trashed)->update(['intro' => '<img src="'.$inlineOnly->url.'">']);
        DB::table('posts')->where('id', $active)->update(['conclusion' => '<img src="'.$intro->url.'?v=1">']);
        $item = $this->blockItem($active);
        DB::table('block_items')->where('id', $item)->update([
            'value' => json_encode(['value' => '<img src="'.str_replace(' ', '%20', $block->url).'">']),
        ]);
        DB::table('pages')->insert(['content' => '<img src="'.$page->url.'">']);
        DB::table('faqs')->insert(['post_id' => $active, 'question' => 'Image?', 'answer' => '<img src="'.$faq->url.'">', 'order' => 1]);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 1 images; preserved 4 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('attachments', ['id' => $inlineOnly->id]);
        foreach ([$intro, $block, $page, $faq] as $image) {
            $this->assertDatabaseHas('attachments', ['id' => $image->id]);
            Storage::disk('aimages')->assertExists($image->name);
        }
    }

    /** @dataProvider authorAttachmentGroups */
    public function test_images_shared_with_an_author_are_preserved(string $group): void
    {
        $trashed = $this->createPost(true);
        $image = $this->image('author-'.$group.'.jpg', $trashed);
        // Use the same numeric ID to verify that the owner type matters too.
        $author = (new Author)->forceFill(['id' => $trashed]);
        $author->addAttachment(['id' => $image->id], $group);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 1 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        $this->assertDatabaseHas('attachmentables', [
            'attachment_id' => $image->id,
            'attachmentable_type' => Author::class,
            'attachmentable_id' => $author->id,
            'group' => $group,
        ]);
        $this->assertDatabaseHas('attachmentables', [
            'attachment_id' => $image->id,
            'attachmentable_type' => Post::class,
            'attachmentable_id' => $trashed,
        ]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public static function authorAttachmentGroups(): array
    {
        return ['avatar' => ['avatar'], 'meta thumbnail' => ['meta_thumbnail']];
    }

    public function test_images_attached_only_to_an_author_are_not_cleanup_candidates(): void
    {
        $image = $this->image('author-only.jpg');
        $author = (new Author)->forceFill(['id' => 1]);
        $author->addAttachment(['id' => $image->id], 'avatar');

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 0 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        $this->assertDatabaseHas('attachmentables', [
            'attachment_id' => $image->id, 'attachmentable_type' => Author::class,
        ]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public function test_dry_run_changes_neither_files_nor_database_records(): void
    {
        $image = $this->image('preview.jpg', $this->createPost(true));

        $this->artisan('posts:clean-images', ['--dry-run' => true])
            ->expectsOutput("Would delete image #{$image->id}: preview.jpg")
            ->expectsOutput('Would delete 1 images; preserved 0 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        $this->assertDatabaseHas('attachmentables', ['attachment_id' => $image->id]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    /** @dataProvider inlineUrlCases */
    public function test_preserves_images_in_different_inline_url_formats(string $name, string $template): void
    {
        $image = $this->image($name, $this->createPost(true));
        $active = $this->createPost(false);
        DB::table('posts')->where('id', $active)->update(['intro' => sprintf($template, $image->url)]);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 1 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public static function inlineUrlCases(): array
    {
        return [
            'literal space' => ['a space.jpg', '<img src="%s">'],
            'punctuation' => ['image(1),2.jpg', '<img src="%s">'],
            'srcset' => ['first.jpg', '<img srcset="%s 1x, /storage/attachments/images/second.jpg 2x">'],
            'css' => ['background.jpg', '<div style="background-image: url(%s)"></div>'],
            'query and fragment' => ['version.jpg', '<img src="%s?v=1#preview">'],
        ];
    }

    public function test_page_content_blocks_protect_images_without_attachment_links(): void
    {
        $image = $this->image('page-block.jpg', $this->createPost(true));
        $page = DB::table('pages')->insertGetId([]);
        $block = DB::table('content_blocks')->insertGetId([
            'blockable_type' => Page::class, 'blockable_id' => $page,
            'name' => 'Content', 'ident' => 'content', 'order' => 1,
        ]);
        DB::table('block_items')->insert([
            'block_id' => $block, 'type' => 'text', 'order' => 1,
            'value' => json_encode(['value' => '<img src="'.$image->url.'">']),
        ]);

        $this->artisan('posts:clean-images')->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public function test_missing_files_are_cleaned_and_rerunning_is_harmless(): void
    {
        $image = $this->image('missing.jpg', $this->createPost(true));
        Storage::disk('aimages')->delete($image->name);

        $this->artisan('posts:clean-images')->assertSuccessful();
        $this->assertDatabaseMissing('attachments', ['id' => $image->id]);
        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 0 shared images; 0 failed.')
            ->assertSuccessful();
    }

    /** @dataProvider retainedTextColumns */
    public function test_other_rich_text_content_protects_shared_images(string $table, string $column): void
    {
        $image = $this->image('retained.jpg', $this->createPost(true));
        DB::table($table)->insert([$column => '<img src="'.$image->url.'">']);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 1 shared images; 0 failed.')
            ->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public static function retainedTextColumns(): array
    {
        return [
            ['authors', 'description'],
            ['authors', 'description_small'],
            ['categories', 'description'],
            ['games', 'description'],
            ['games', 'summary'],
        ];
    }

    public function test_page_block_json_protects_shared_images(): void
    {
        $image = $this->image('page-data.jpg', $this->createPost(true));
        $page = DB::table('pages')->insertGetId([]);
        DB::table('page_blocks')->insert([
            'page_id' => $page,
            'data' => json_encode(['body' => ['value' => '<img src="'.$image->url.'">']]),
        ]);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 1 shared images; 0 failed.')
            ->assertSuccessful();

        Storage::disk('aimages')->assertExists($image->name);
        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
    }

    public function test_attachments_on_trashed_content_blocks_are_cleaned(): void
    {
        $image = $this->image('content-block.jpg');
        $item = $this->blockItem($this->createPost(true));
        $block = DB::table('block_items')->where('id', $item)->value('block_id');
        $this->attach($image, $block, ContentBlock::class);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 1 images; preserved 0 shared images; 0 failed.')
            ->assertSuccessful();

        Storage::disk('aimages')->assertMissing($image->name);
        $this->assertDatabaseMissing('attachments', ['id' => $image->id]);
        $this->assertDatabaseHas('content_blocks', ['id' => $block]);
        $this->assertDatabaseHas('block_items', ['id' => $item]);
    }

    public function test_morph_aliases_are_supported(): void
    {
        Relation::morphMap(['post' => Post::class, 'block' => ContentBlock::class, 'item' => BlockItem::class]);

        try {
            $trashed = $this->createPost(true);
            $thumbnail = $this->image('alias-thumbnail.jpg');
            $this->attach($thumbnail, $trashed, 'post');
            $image = $this->image('alias-item.jpg');
            $item = $this->blockItem($trashed);
            DB::table('content_blocks')->update(['blockable_type' => 'post']);
            $this->attach($image, $item, 'item');

            $this->artisan('posts:clean-images')
                ->expectsOutput('Deleted 2 images; preserved 0 shared images; 0 failed.')
                ->assertSuccessful();

            Storage::disk('aimages')->assertMissing($thumbnail->name);
            Storage::disk('aimages')->assertMissing($image->name);
            $this->assertDatabaseCount('attachments', 0);
        } finally {
            Relation::morphMap([], false);
        }
    }

    public function test_a_file_shared_by_different_attachment_records_is_preserved(): void
    {
        $image = $this->image('duplicate.jpg', $this->createPost(true));
        $this->image('duplicate.jpg', $this->createPost(false));

        $this->artisan('posts:clean-images')->assertSuccessful();

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        Storage::disk('aimages')->assertExists($image->name);
    }

    public function test_failed_file_deletion_preserves_attachment_and_links(): void
    {
        $image = $this->image('failure.jpg', $this->createPost(true));
        $disk = Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('url')->with('')->andReturn('/storage/attachments/images/');
        $disk->shouldReceive('exists')->with($image->name)->andReturn(true);
        $disk->shouldReceive('delete')->with($image->name)->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('aimages')->andReturn($disk);

        $this->artisan('posts:clean-images')
            ->expectsOutput('Deleted 0 images; preserved 0 shared images; 1 failed.')
            ->assertExitCode(1);

        $this->assertDatabaseHas('attachments', ['id' => $image->id]);
        $this->assertDatabaseHas('attachmentables', ['attachment_id' => $image->id]);
    }

    /** @dataProvider invalidBatchSizes */
    public function test_rejects_invalid_batch_size(string $size): void
    {
        $this->artisan('posts:clean-images', ['--batch-size' => $size])
            ->expectsOutput('The --batch-size option must be a positive integer.')
            ->assertExitCode(2);
    }

    public static function invalidBatchSizes(): array
    {
        return [['0'], ['-1'], ['1.5'], ['invalid'], ['']];
    }

    private function createPost(bool $trashed): int
    {
        return DB::table('posts')->insertGetId(['deleted_at' => $trashed ? now() : null]);
    }

    private function image(string $name, ?int $post = null, string $type = 'image'): Attachment
    {
        $image = Attachment::create(['name' => $name, 'original_name' => $name, 'type' => $type]);
        Storage::disk(Attachment::disk($type))->put($name, 'image contents');
        if ($post !== null) {
            $this->attach($image, $post);
        }

        return $image;
    }

    private function attach(Attachment $image, ?int $owner, ?string $type = Post::class): void
    {
        DB::table('attachmentables')->insert([
            'attachment_id' => $image->id, 'attachmentable_id' => $owner,
            'attachmentable_type' => $type, 'group' => 'image',
        ]);
    }

    private function blockItem(int $post): int
    {
        $block = DB::table('content_blocks')->insertGetId([
            'blockable_type' => Post::class, 'blockable_id' => $post,
            'name' => 'Content', 'ident' => 'content', 'order' => 1,
        ]);

        return DB::table('block_items')->insertGetId(['block_id' => $block, 'type' => 'text', 'order' => 1]);
    }
}
