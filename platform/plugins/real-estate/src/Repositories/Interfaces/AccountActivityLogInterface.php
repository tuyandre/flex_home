<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountActivityLogInterface extends RepositoryInterface
{
    public function getAllLogs(int $accountId, int $paginate = 10): LengthAwarePaginator;
}
