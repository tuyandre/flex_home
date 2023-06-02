<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\RealEstate\Models\Account;
use Illuminate\Http\Resources\Json\JsonResource;
use Botble\RealEstate\Facades\RealEstateHelper;

/**
 * @mixin Account
 */
class AccountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'avatar' => $this->avatar_url,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'description' => $this->description,
            ] + (RealEstateHelper::isEnabledCreditsSystem() ? ['credits' => $this->credits] : []);
    }
}
