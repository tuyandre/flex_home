<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class FeatureRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'icon' => 'string|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('plugins/real-estate::feature.messages.request.name_required'),
        ];
    }
}
