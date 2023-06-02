<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends BaseModel
{
    protected $table = 're_reviews';

    protected $fillable = [
        'account_id',
        'reviewable_type',
        'reviewable_id',
        'star',
        'content',
        'status',
    ];

    protected $casts = [
        'star' => 'int',
        'status' => ReviewStatusEnum::class,
        'content' => SafeContent::class,
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
