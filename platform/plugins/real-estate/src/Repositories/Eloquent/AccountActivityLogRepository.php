<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountActivityLogRepository extends RepositoriesAbstract implements AccountActivityLogInterface
{
    public function getAllLogs(int $accountId, int $paginate = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('account_id', $accountId)
            ->latest('created_at')
            ->paginate($paginate);
    }
}
