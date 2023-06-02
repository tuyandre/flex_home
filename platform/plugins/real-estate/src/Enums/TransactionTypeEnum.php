<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static TransactionTypeEnum REMOVE()
 * @method static TransactionTypeEnum ADD()
 */
class TransactionTypeEnum extends Enum
{
    public const ADD = 'add';
    public const REMOVE = 'remove';

    public static $langPath = 'plugins/real-estate::transaction.types';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::REMOVE => Html::tag('span', self::REMOVE()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::ADD => Html::tag('span', self::ADD()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
