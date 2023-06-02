<?php

namespace Botble\RealEstate\Contracts;

trait Typeable
{
    public function stringToArray(string|null $string): array
    {
        if ($string === null) {
            return [];
        }

        return explode(',', $string);
    }

    public function yesNoToBoolean(string|null $string): bool
    {
        return $string === 'yes';
    }
}
