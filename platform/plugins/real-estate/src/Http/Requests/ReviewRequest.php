<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reviewable_type' => Rule::in([Property::class, Project::class]),
            'star' => 'required|int|min:1|max:5',
            'content' => 'required|string|min:4|max:500',
        ];
    }
}
