<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;

class SendConsultRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:220',
            'email' => 'required|email',
            'content' => 'required|string',
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'content' => __('Content'),
        ] + (is_plugin_active('captcha') ? Captcha::attributes() : []);
    }
}
