<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static PropertyPeriodEnum DAY()
 * @method static PropertyPeriodEnum MONTH()
 * @method static PropertyPeriodEnum YEAR()
 */
class PropertyPeriodEnum extends Enum
{
    public const DAY = 'day';
    public const MONTH = 'month';
    public const YEAR = 'year';

    public static $langPath = 'plugins/real-estate::property.periods';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::DAY => Html::tag('span', self::DAY()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            self::MONTH => Html::tag('span', self::MONTH()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            self::YEAR => Html::tag('span', self::YEAR()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
