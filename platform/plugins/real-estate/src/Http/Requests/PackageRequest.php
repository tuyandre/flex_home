<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PackageRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:220',
            'price' => 'numeric|required|min:0',
            'percent_save' => 'numeric|required|min:0',
            'currency_id' => 'required|integer',
            'number_of_listings' => 'numeric|required|min:1',
            'order' => 'required|integer|min:0|max:127',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
