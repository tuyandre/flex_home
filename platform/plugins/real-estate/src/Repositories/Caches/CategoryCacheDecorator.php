<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryCacheDecorator extends CacheAbstractDecorator implements CategoryInterface
{
    public function getCategories(array $select, array $orderBy): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
