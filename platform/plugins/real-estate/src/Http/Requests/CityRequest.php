<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CityRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:220',
            'state_id' => 'required|integer',
            'country_id' => 'required|integer',
            'slug' => 'required|string|unique:cities,slug,' . $this->route('city'),
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
