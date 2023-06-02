<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Botble\Base\Facades\Html;
use Illuminate\Support\HtmlString;

/**
 * @method static ConsultStatusEnum UNREAD()
 * @method static ConsultStatusEnum READ()
 */
class ConsultStatusEnum extends Enum
{
    public const READ = 'read';
    public const UNREAD = 'unread';

    public static $langPath = 'plugins/real-estate::consult.statuses';

    public function toHtml(): HtmlString|string|null
    {
        return match ($this->value) {
            self::UNREAD => Html::tag('span', self::UNREAD()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::READ => Html::tag('span', self::READ()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            default => null,
        };
    }
}
