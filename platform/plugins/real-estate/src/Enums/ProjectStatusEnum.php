<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static ProjectStatusEnum NOT_AVAILABLE()
 * @method static ProjectStatusEnum PRE_SALE()
 * @method static ProjectStatusEnum SELLING()
 * @method static ProjectStatusEnum SOLD()
 * @method static ProjectStatusEnum BUILDING()
 */
class ProjectStatusEnum extends Enum
{
    public const NOT_AVAILABLE = 'not_available';
    public const PRE_SALE = 'pre_sale';
    public const SELLING = 'selling';
    public const SOLD = 'sold';
    public const BUILDING = 'building';

    public static $langPath = 'plugins/real-estate::project.statuses';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::NOT_AVAILABLE => Html::tag(
                'span',
                self::NOT_AVAILABLE()->label(),
                ['class' => 'label-default status-label']
            )
                ->toHtml(),
            self::PRE_SALE => Html::tag('span', self::PRE_SALE()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::SELLING => Html::tag('span', self::SELLING()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            self::SOLD => Html::tag('span', self::SOLD()->label(), ['class' => 'label-danger status-label'])
                ->toHtml(),
            self::BUILDING => Html::tag('span', self::BUILDING()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
