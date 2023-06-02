<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\RealEstate\Models\AccountActivityLog;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AccountActivityLog
 */
class ActivityLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'description' => $this->getDescription(),
        ];
    }
}
