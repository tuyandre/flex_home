<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProjectInterface extends RepositoryInterface
{
    public function getProjects(array $filters = [], array $params = []): Collection|LengthAwarePaginator;

    public function getRelatedProjects(int $projectId, int $limit = 4, array $with = []): Collection|LengthAwarePaginator;
}
