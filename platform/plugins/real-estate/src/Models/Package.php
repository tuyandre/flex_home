<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends BaseModel
{
    protected $table = 're_packages';

    protected $fillable = [
        'name',
        'price',
        'currency_id',
        'percent_save',
        'number_of_listings',
        'account_limit',
        'order',
        'is_default',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 're_account_packages', 'package_id', 'account_id');
    }

    public function getPriceFormatAttribute(): ?string
    {
        if ($this->price_formatted) {
            return $this->price_formatted;
        }

        $currency = $this->currency;

        if (! $currency || ! $currency->id) {
            $currency = get_application_currency();
        }

        return $this->price_formatted = format_price($this->price, $currency, fullNumber: true);
    }

    public function getPricePerListingFormatAttribute(): ?string
    {
        if ($this->price_per_listing_formatted) {
            return $this->price_per_listing_formatted;
        }

        $currency = $this->currency;

        if (! $currency || ! $currency->id) {
            $currency = get_application_currency();
        }

        return $this->price_per_listing_formatted = format_price($this->price / $this->number_of_listings, $currency, fullNumber: true);
    }
}
