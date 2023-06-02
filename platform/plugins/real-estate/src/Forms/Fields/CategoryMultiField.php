<?php

namespace Botble\RealEstate\Forms\Fields;

use Botble\Base\Forms\FormField;

class CategoryMultiField extends FormField
{
    protected function getTemplate(): string
    {
        return 'plugins/real-estate::categories.categories-multi';
    }
}
