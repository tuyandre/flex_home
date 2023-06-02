<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Http\Requests\CustomFieldRequest;
use Botble\RealEstate\Models\CustomField;

class CustomFieldForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScripts(['jquery-ui'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/global-custom-fields.js',
            ]);

        $this
            ->setupModel(new CustomField())
            ->setValidatorClass(CustomFieldRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('type', 'customSelect', [
                'label' => trans('plugins/real-estate::custom-fields.type'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => ['class' => 'form-control custom-field-type'],
                'choices' => CustomFieldEnum::labels(),
            ])
            ->setBreakFieldPoint('type')
            ->addMetaBoxes([
                'custom_fields_box' => [
                    'attributes' => [
                        'id' => 'custom_fields_box',
                    ],
                    'id' => 'custom_fields_box',
                    'title' => trans('plugins/real-estate::custom-fields.options'),
                    'content' => view(
                        'plugins/real-estate::custom-fields.options',
                        ['options' => $this->model->options->sortBy('order')]
                    )->render(),
                ],
            ]);
    }
}
