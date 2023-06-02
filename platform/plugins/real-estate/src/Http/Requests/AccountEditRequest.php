<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AccountEditRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:120|min:2',
            'last_name' => 'required|string|max:120|min:2',
            'username' => 'required|string|max:60|min:2|unique:re_accounts,username,' . $this->route('account'),
            'email' => 'required|max:60|min:6|email|unique:re_accounts,email,' . $this->route('account'),
        ];

        if ($this->boolean('is_change_password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        return $rules;
    }
}
