<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Illuminate\Support\Collection;

class CurrencyCacheDecorator extends CacheAbstractDecorator implements CurrencyInterface
{
    public function getAllCurrencies(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
