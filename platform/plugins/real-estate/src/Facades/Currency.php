<?php

namespace Botble\RealEstate\Facades;

use Botble\RealEstate\Supports\CurrencySupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setApplicationCurrency(\Botble\RealEstate\Models\Currency $currency)
 * @method static \Botble\RealEstate\Models\Currency|null getApplicationCurrency()
 * @method static \Botble\RealEstate\Models\Currency|null getDefaultCurrency()
 * @method static \Illuminate\Support\Collection currencies()
 * @method static string|null detectedCurrencyCode()
 *
 * @see \Botble\RealEstate\Supports\CurrencySupport
 */
class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrencySupport::class;
    }
}
