<?php

namespace Botble\Base\Supports\ValueObjects;

use DateTimeInterface;

class ProductUpdate
{
    public function __construct(
        public string $updateId,
        public string $version,
        public DateTimeInterface $releasedDate,
        public string|null $summary = null,
        public string|null $changelog = null,
        public bool $hasSQL = false
    ) {
    }
}
