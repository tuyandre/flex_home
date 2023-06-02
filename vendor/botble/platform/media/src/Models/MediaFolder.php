<?php

namespace Botble\Media\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Botble\Media\Facades\RvMedia;

class MediaFolder extends BaseModel
{
    use SoftDeletes;

    protected $table = 'media_folders';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'user_id',
    ];

    protected $casts = [
        'name' => SafeContent::class,
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'folder_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id')->withDefault();
    }

    protected function parents(): Attribute
    {
        return Attribute::make(
            get: function (): Collection {
                $parents = collect();

                $parent = $this->parent;

                while ($parent->id) {
                    $parents->push($parent);
                    $parent = $parent->parent;
                }

                return $parents;
            },
        );
    }

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function (MediaFolder $folder) {
            if ($folder->isForceDeleting()) {
                $files = MediaFile::where('folder_id', $folder->getKey())->onlyTrashed()->get();

                foreach ($files as $file) {
                    RvMedia::deleteFile($file);
                    $file->forceDelete();
                }
            } else {
                $files = MediaFile::where('folder_id', $folder->getKey())->withTrashed()->get();

                foreach ($files as $file) {
                    $file->delete();
                }
            }
        });

        static::restoring(function (MediaFolder $folder) {
            MediaFile::where('folder_id', $folder->getKey())->restore();
        });
    }
}
