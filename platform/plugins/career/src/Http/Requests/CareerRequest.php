<?php

namespace Botble\Career\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CareerRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'location' => 'required|string',
            'salary' => 'required|string',
            'description' => 'nullable|string|max:400',
            'content' => 'required|string|max:1200',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
