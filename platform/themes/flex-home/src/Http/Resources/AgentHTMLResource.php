<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\RealEstate\Models\Account;
use Illuminate\Http\Resources\Json\JsonResource;
use Botble\Theme\Facades\Theme;

/**
 * @mixin Account
 */
class AgentHTMLResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'HTML' => Theme::partial('real-estate.agents.item', ['account' => $this]),
        ];
    }
}
