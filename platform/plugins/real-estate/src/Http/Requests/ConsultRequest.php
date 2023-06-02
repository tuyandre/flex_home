<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ConsultRequest extends Request
{
    public function rules(): array
    {
        return [
            'status' => Rule::in(ConsultStatusEnum::values()),
        ];
    }
}
