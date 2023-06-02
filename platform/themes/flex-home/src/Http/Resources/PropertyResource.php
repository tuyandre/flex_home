<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\RealEstate\Models\Property;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Property
 */
class PropertyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description,
            'image' => $this->image_small,
            'image_thumb' => $this->image_thumb,
            'images' => $this->images,
            'price_html' => $this->price_html,
            'city_name' => $this->city->name ? $this->city->name . ', ' . $this->state->name : $this->state->name,
            'number_bedroom' => $this->number_bedroom,
            'number_bathroom' => $this->number_bathroom,
            'square' => $this->square,
            'square_text' => $this->square_text,
            'type' => $this->type,
            'type_text' => $this->type_html,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'period' => $this->period,
            'status_html' => $this->status_html,
            'category_name' => $this->category_name,
            'map_icon' => $this->map_icon,
        ];
    }
}
