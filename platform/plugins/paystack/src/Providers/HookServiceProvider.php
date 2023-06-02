<?php

namespace Botble\Paystack\Providers;

use Botble\Payment\Enums\PaymentMethodEnum;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Paystack;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerPaystackMethod'], 16, 2);
        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPaystack'], 16, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 97, 1);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['PAYSTACK'] = PAYSTACK_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 21, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYSTACK_PAYMENT_METHOD_NAME) {
                $value = 'Paystack';
            }

            return $value;
        }, 21, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYSTACK_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 21, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . view('plugins/paystack::settings')->render();
    }

    public function registerPaystackMethod(?string $html, array $data): string
    {
        return $html . view('plugins/paystack::methods', $data)->render();
    }

    public function checkoutWithPaystack(array $data, Request $request)
    {
        if ($request->input('payment_method') == PAYSTACK_PAYMENT_METHOD_NAME) {
            try {
                $response = Paystack::getAuthorizationResponse([
                    'reference' => Paystack::genTranxRef(),
                    'quantity' => 1,
                    'currency' => $request->input('currency'),
                    'amount' => $request->input('amount') * 100,
                    'email' => auth('account')->check() ? auth('account')->user()->email : null,
                    'callback_url' => route('paystack.payment.callback'),
                    'metadata' => json_encode([
                        'order_id' => [session('subscribed_packaged_id')],
                        'return_url' => $request->input('return_url'),
                        'callback_url' => $request->input('callback_url'),
                        'customer_id' => $request->input('customer_id'),
                        'customer_type' => $request->input('customer_type'),
                    ]),
                ]);

                if ($response['status']) {
                    header('Location: ' . $response['data']['authorization_url']);
                    exit;
                }

                $data['error'] = true;
                $data['message'] = __('Payment failed!');
            } catch (Throwable $exception) {
                $data['error'] = true;
                $data['message'] = json_encode($exception->getMessage());
            }
        }

        return $data;
    }
}
