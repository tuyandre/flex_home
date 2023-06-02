<?php

namespace Botble\RealEstate\Contracts;

use Botble\Base\Models\BaseModel;
use Illuminate\Support\Collection;

trait OnSuccesses
{
    protected array $successes = [];

    public function onSuccess(BaseModel ...$model): void
    {
        $this->successes = array_merge($this->successes, $model);
    }

    public function successes(): Collection
    {
        return new Collection($this->successes);
    }
}
