<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static ReviewStatusEnum PENDING()
 * @method static ReviewStatusEnum APPROVED()
 * @method static ReviewStatusEnum REJECTED()
 */
class ReviewStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    public static $langPath = 'plugins/real-estate::review.moderation-statuses';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::APPROVED => Html::tag('span', self::APPROVED()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::REJECTED => Html::tag('span', self::REJECTED()->label(), ['class' => 'label-danger status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
