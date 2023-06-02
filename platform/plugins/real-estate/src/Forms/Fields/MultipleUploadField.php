<?php

namespace Botble\RealEstate\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;

class MultipleUploadField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addScriptsDirectly('vendor/core/core/media/libraries/dropzone/dropzone.js')
            ->addStylesDirectly('https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css');

        return 'plugins/real-estate::account.forms.fields.multiple-upload';
    }
}
