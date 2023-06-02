<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface AccountInterface extends RepositoryInterface
{
    public function createUsername(string $name, ?int $id = null): string;
}
