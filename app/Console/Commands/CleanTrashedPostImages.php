<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Services\TrashedPostImageReferences;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class CleanTrashedPostImages extends Command
{
    protected $signature = 'posts:clean-images
                            {--dry-run : List eligible images without deleting anything}
                            {--batch-size=100 : Number of image records to inspect per batch}';

    protected $description = 'Delete images used only by trashed posts, preserving shared images';

    public function handle(TrashedPostImageReferences $references): int
    {
        $batchSize = filter_var($this->option('batch-size'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($batchSize === false) {
            $this->error('The --batch-size option must be a positive integer.');

            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');
        $deleted = $shared = $failed = 0;
        $maxId = Attachment::max('id') ?? 0;

        Attachment::where('type', 'image')->where('id', '<=', $maxId)
            ->chunkById($batchSize, function ($attachments) use ($references, $dryRun, &$deleted, &$shared, &$failed) {
                $inline = $references->inlineImages();

                foreach ($attachments as $attachment) {
                    try {
                        $result = DB::transaction(function () use ($attachment, $references, $inline, $dryRun) {
                            $image = Attachment::whereKey($attachment->id)->lockForUpdate()->first();
                            if (! $image || $image->type !== 'image') {
                                return 'unrelated';
                            }

                            $owners = $image->attachmentables()->get();
                            // Compare IDs instead of negating nullable polymorphic columns.
                            $trashedOwners = $image->attachmentables()
                                ->where(fn ($query) => $references->trashedOwners($query))->pluck('id');
                            if ($trashedOwners->isEmpty() && ! isset($inline['trashed'][$image->name])) {
                                return 'unrelated';
                            }

                            if ($owners->count() !== $trashedOwners->count()
                                || isset($inline['retained'][$image->name])
                                || Attachment::where('type', 'image')->where('name', $image->name)->whereKeyNot($image->id)->exists()) {
                                return 'shared';
                            }

                            if ($dryRun) {
                                $this->line("Would delete image #{$image->id}: {$image->name}");

                                return 'deleted';
                            }

                            $storage = $image->getStorage();
                            if ($storage->exists($image->name) && ! $storage->delete($image->name)) {
                                throw new \RuntimeException('The image file could not be deleted.');
                            }

                            $image->attachmentables()->delete();
                            $image->delete();
                            $this->line("Deleted image #{$image->id}: {$image->name}");

                            return 'deleted';
                        });

                        $deleted += $result === 'deleted' ? 1 : 0;
                        $shared += $result === 'shared' ? 1 : 0;
                    } catch (Throwable $exception) {
                        $failed++;
                        $this->error("Image #{$attachment->id}: {$exception->getMessage()}");
                    }
                }
            });

        $action = $dryRun ? 'Would delete' : 'Deleted';
        $this->info("{$action} {$deleted} images; preserved {$shared} shared images; {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
