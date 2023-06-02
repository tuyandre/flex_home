<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface CategoryInterface extends RepositoryInterface
{
    public function getCategories(array $select, array $orderBy): Collection;
}
