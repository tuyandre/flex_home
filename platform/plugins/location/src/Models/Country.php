<?php

namespace Botble\Location\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends BaseModel
{
    protected $table = 'countries';

    protected $fillable = [
        'name',
        'nationality',
        'code',
        'order',
        'is_default',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'nationality' => SafeContent::class,
        'code' => SafeContent::class,
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function (Country $country) {
            State::where('country_id', $country->id)->delete();
            City::where('country_id', $country->id)->delete();
        });
    }
}
