<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\RealEstate\Models\Package;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Package
 */
class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'price_text' => $this->price_format,
            'price_per_post_text' => $this->price_per_listing_format . ' / ' . trans('plugins/real-estate::dashboard.per_post'),
            'percent_save' => $this->percent_save,
            'number_of_listings' => $this->number_of_listings,
            'number_posts_free' => trans('plugins/real-estate::dashboard.number_posts_free', ['posts' => $this->number_of_listings]),
            'price_text_with_sale_off' => trans('plugins/real-estate::dashboard.total_package_price', ['total' => $this->price_format, 'percent' => $this->percent_save]),
        ];
    }
}
