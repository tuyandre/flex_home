<?php

namespace Botble\Location\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Illuminate\Database\Eloquent\Collection;

class CityCacheDecorator extends CacheAbstractDecorator implements CityInterface
{
    public function filters(string|null $keyword, int|null $limit = 10, array $with = [], array $select = ['cities.*']): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
