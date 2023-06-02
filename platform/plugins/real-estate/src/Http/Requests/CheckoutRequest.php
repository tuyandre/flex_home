<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|' . Rule::in(PaymentMethodEnum::values()),
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string',
        ];
    }
}
