<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\TransactionTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends Request
{
    public function rules(): array
    {
        return [
            'credits' => 'required|numeric|min:1',
            'type' => Rule::in(TransactionTypeEnum::values()),
        ];
    }
}
