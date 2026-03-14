<?php

namespace App\Traits;

use App\Models\Attachment;
use App\Models\Attachmentable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait HasAttachments
{
    /**
     * Attach the file to the model.
     * We can attach multiple or one file.
     * If we attach single one file, then previous attachment is detached automaticaly.
     * We may attach Illuminate\Http\UploadedFile or array.
     * Array should contain key 'file' or key 'id'.
     */
    public function addAttachment($attachment, string $group, ?string $forceFileName = null)
    {
        // dlog("HasAttachments@addAttachment $group | $this->id | " . self::class . ': ' . json_encode($attachment) ); //! LOG

        if (! $attachment) {
            // dlog(" empty"); //! LOG
            return [];
        }

        $attachment = $this->transform($attachment);
        // dlog(" transformed: " . json_encode($attachment) ); //! LOG
        $isMultiple = ! array_key_exists('id', $attachment) && ! array_key_exists('file', $attachment);
        $savedAttachments = [];
        $existed = Attachmentable::getByModel($this, $group, false);

        if ($isMultiple) {
            // dlog(" is mult"); //! LOG
            $attachments = $attachment;
            $existed->whereNotIn('attachment_id', array_column($attachments, 'id'))->delete();
            // dlog("  to delete: ", $existed->whereNotIn('attachment_id', array_column($attachments, 'id'))->get()->toArray()); //! LOG
        } else {
            // dlog(" is one"); //! LOG
            $attachment['id'] = $attachment['id'] ?? false;
            $attachment['file'] = $attachment['file'] ?? false;
            $attachment['id_old'] = $attachment['id_old'] ?? false;

            if (! $attachment['id'] && ! $attachment['file'] && ! $attachment['id_old']) {
                // dlog(" empty"); //! LOG
                return [];
            }

            $attachments = [$attachment];

            if (! $attachment['id'] || $attachment['id_old'] != $attachment['id']) {
                $existed->delete();
            }
        }

        // dlog(' Attachments: ', $attachment); //! LOG

        foreach ($attachments as $i => $attachment) {
            // dlog("  $i"); //! LOG
            $attachmentModel = isset($attachment['id']) ? Attachment::find($attachment['id']) : null;
            $attachmentIdOld = $attachment['id_old'] ?? null;

            if ($attachmentModel && $attachmentModel->id == $attachmentIdOld) {
                $savedAttachments[] = $attachmentModel;

                // dlog("  same id"); //! LOG
                continue;
            }

            if ($attachmentModel && $attachmentModel->id != $attachmentIdOld) {
                Attachmentable::create([
                    'attachment_id' => $attachmentModel->id,
                    'attachmentable_id' => $this->id,
                    'attachmentable_type' => get_class($this),
                    'group' => $group,
                ]);
                $savedAttachments[] = $attachmentModel;

                // dlog("  new id"); //! LOG
                continue;
            }

            $uploadedFile = $attachment['file'];
            $type = $this->determineType($uploadedFile->extension());
            $disk = Attachment::disk($type);
            $fileName = $forceFileName ? ($forceFileName.'.'.$uploadedFile->extension()) : $uploadedFile->getClientOriginalName();
            $og_name = Attachment::makeUniqueName($fileName, $disk);
            $attachment['alt'] ??= readable(strstr($og_name, '.', true));
            $attachment['title'] ??= $attachment['alt'];
            $path = $uploadedFile->storeAs('', $og_name, $disk);

            $attachmentModel = Attachment::create([
                'name' => $path,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'type' => $type,
                'alt' => $attachment['alt'],
                'title' => $attachment['title'],
                'size' => $uploadedFile->getSize(),
            ]);
            Attachmentable::create([
                'attachment_id' => $attachmentModel->id,
                'attachmentable_id' => $this->id,
                'attachmentable_type' => get_class($this),
                'group' => $group,
            ]);
            $savedAttachments[] = $attachmentModel;
            // dlog("  new file"); //! LOG
        }

        // check for dublicates (possible unknown bug)
        $all = Attachmentable::getByModel($this, $group, false)->get();
        $unique = [];
        foreach ($all as $attachmentable) {
            if (! isset($unique[$attachmentable->attachment_id])) {
                $unique[$attachmentable->attachment_id] = 1;

                continue;
            }

            $attachmentable->delete();
        }

        return $savedAttachments;
    }

    private function transform($attachment)
    {
        if (is_string($attachment) && $this->isUrlString($attachment)) {
            $downloadedFile = $this->downloadUrlAsUploadedFile($attachment);

            return $downloadedFile ? ['file' => $downloadedFile] : [];
        }

        if (! is_array($attachment)) {
            // it is one attachment as UploadedFile instance
            return ['file' => $attachment];
        }

        if (array_key_exists('id', $attachment) || array_key_exists('file', $attachment)) {
            // it is one attachment in array form
            return $attachment;
        }

        if (array_key_exists('id', $attachment[0]) || array_key_exists('file', $attachment[0])) {
            // it is few attachments in array form
            return $attachment;
        }

        // it is few attachments as UploadedFile instances
        $result = [];
        foreach ($attachment as $a) {
            $result[] = ['file' => $a];
        }

        return $result;
    }

    private function determineType($ext)
    {
        if (in_array($ext, ['jpeg', 'gif', 'png', 'jpg', 'webp'])) {
            $type = 'image';
        } elseif (in_array($ext, ['doc', 'docx', 'pdf'])) {
            $type = 'document';
        } elseif (in_array($ext, ['mov', 'mp4', 'avi', 'ogg', 'wmv', 'webm', 'mkv'])) {
            $type = 'video';
        } else {
            $type = 'file';
        }

        return $type;
    }

    private function isUrlString(string $value): bool
    {
        return filter_var(trim($value), FILTER_VALIDATE_URL) !== false;
    }

    private function downloadUrlAsUploadedFile(string $url): ?UploadedFile
    {
        if (! $this->isSupportedImageResponse($url)) {
            return null;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'att_');
        if ($tmpPath === false) {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->sink($tmpPath)
                ->get($url);
        } catch (\Throwable $e) {
            @unlink($tmpPath);

            return null;
        }

        if (! $response->successful()) {
            @unlink($tmpPath);

            return null;
        }

        $mime = $this->normalizeMimeType($response->header('Content-Type'));

        if (! $this->isSupportedImageResponse($url, $mime)) {
            @unlink($tmpPath);

            return null;
        }

        $originalName = $this->makeDownloadedFileName($url, $mime);

        return new UploadedFile($tmpPath, $originalName, $mime ?: null, null, true);
    }

    private function isSupportedImageResponse(string $url, ?string $mime = null): bool
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        if ($extension !== '' && ! $this->isSupportedImageExtension($extension)) {
            return false;
        }

        if ($mime !== null) {
            return $this->isSupportedImageMime($mime);
        }

        return $extension !== '';
    }

    private function isSupportedImageExtension(string $extension): bool
    {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    private function isSupportedImageMime(string $mime): bool
    {
        return in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true);
    }

    private function normalizeMimeType(?string $mime): ?string
    {
        if (! $mime) {
            return null;
        }

        return strtolower(trim(explode(';', $mime)[0]));
    }

    private function makeDownloadedFileName(string $url, ?string $mime): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $baseName = basename($path) ?: 'downloaded-file';
        $extension = pathinfo($baseName, PATHINFO_EXTENSION);

        if ($extension === '') {
            $guessed = $this->guessExtensionFromMime($mime);
            if ($guessed) {
                $baseName .= '.'.$guessed;
            }
        }

        return $baseName;
    }

    private function guessExtensionFromMime(?string $mime): ?string
    {
        $mime = $this->normalizeMimeType($mime);
        if (! $mime) {
            return null;
        }

        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
        ];

        if (isset($map[$mime])) {
            return $map[$mime];
        }

        $fallback = Str::after($mime, '/');
        if ($fallback === '' || str_contains($fallback, 'octet-stream')) {
            return null;
        }

        return $fallback;
    }
}
