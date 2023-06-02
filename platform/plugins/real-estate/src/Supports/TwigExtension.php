<?php

namespace Botble\RealEstate\Supports;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension implements ExtensionInterface
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price_format', 'format_price'),
            new TwigFilter('urlencode', 'urlencode'),
        ];
    }
}
