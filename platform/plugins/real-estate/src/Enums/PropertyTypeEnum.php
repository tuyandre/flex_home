<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static PropertyTypeEnum SALE()
 * @method static PropertyTypeEnum RENT()
 */
class PropertyTypeEnum extends Enum
{
    public const SALE = 'sale';
    public const RENT = 'rent';

    public static $langPath = 'plugins/real-estate::property.types';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::SALE => Html::tag('span', self::SALE()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            self::RENT => Html::tag('span', self::RENT()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
