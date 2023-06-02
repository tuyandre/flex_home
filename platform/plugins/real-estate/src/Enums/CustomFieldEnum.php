<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static CustomFieldEnum TEXT()
 * @method static CustomFieldEnum DROPDOWN()
 */
class CustomFieldEnum extends Enum
{
    public const TEXT = 'text';

    public const DROPDOWN = 'dropdown';

    public static $langPath = 'plugins/real-estate::custom-fields.enums.fields';
}
