<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\RealEstate\Models\Property;
use Illuminate\Http\Resources\Json\JsonResource;
use Botble\Theme\Facades\Theme;

/**
 * @mixin Property
 */
class PropertyHTMLResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'HTML' => Theme::partial('real-estate.properties.item', ['property' => $this]),
        ];
    }
}
