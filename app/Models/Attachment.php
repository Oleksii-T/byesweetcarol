<?php

namespace App\Models;

use App\Traits\LogsActivityBasic;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class Attachment extends Model
{
    use HasFactory;
    use LogsActivityBasic;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'alt',
        'title',
        'group',
        'original_name',
        'type',
        'size',
        'source',
        'attachmentable_id',
        'attachmentable_id_type',
    ];

    protected $appends = [
        'url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $disk = self::disk($model->type);
            Storage::disk($disk)->delete($model->name);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachmentables()
    {
        return $this->hasMany(Attachmentable::class);
    }

    /**
     * @return string
     */
    public function getSize()
    {
        if ($this->size > 1000000) {
            return number_format($this->size / 1000000, 2).' MB';
        }
        if ($this->size > 1000) {
            return number_format($this->size / 1000, 2).' kB';
        }

        return $this->size.' B';
    }

    public function url(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->getStorage()->url($this->name),
        );
    }

    public function getStorage()
    {
        return Storage::disk(self::disk($this->type));
    }

    public static function disk($type): string
    {
        return match ($type) {
            'video' => 'avideos',
            'image' => 'aimages',
            'document' => 'adocuments',
            default => 'attachments',
        };
    }

    public static function makeUniqueName(string $name, string $disk): string
    {
        // dlog(" Attachment@makeUniqueName"); //! LOG
        $i = 1;
        $nameParts = explode('.', $name);
        $extension = array_pop($nameParts);

        // dlog("  check name $name"); //! LOG

        while (Storage::disk($disk)->exists($name)) {
            $name = implode('.', $nameParts)."-$i.$extension";
            // dlog("  check name $name"); //! LOG
            $i++;
        }

        // dlog("  OK: $name"); //! LOG

        return $name;
    }

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->editColumn('size', function ($model) {
                return $model->getSize()." ($model->size)";
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format(env('ADMIN_DATETIME_FORMAT'));
            })
            ->addColumn('action', function ($model) {
                return view('components.admin.actions', [
                    'model' => $model,
                    'name' => 'attachments',
                    'actions' => ['edit'],
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function formatMultipleRichInputRequest($data)
    {
        $result = [];

        foreach ($data['title'] ?? [] as $i => $title) {
            $id = Arr::get($data, "id.$i");
            $idOld = Arr::get($data, "id_old.$i");
            $file = Arr::get($data, "file.$i");
            $alt = $data['alt'][$i];

            if (! $id && ! $file) {
                continue;
            }

            $result[] = [
                'id' => $id,
                'id_old' => $idOld,
                'file' => $file,
                'alt' => $alt,
                'title' => $title,
            ];
        }

        return $result;
    }
}
