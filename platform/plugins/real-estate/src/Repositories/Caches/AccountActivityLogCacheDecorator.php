<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountActivityLogCacheDecorator extends CacheAbstractDecorator implements AccountActivityLogInterface
{
    public function getAllLogs(int $accountId, int $paginate = 10): LengthAwarePaginator
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
