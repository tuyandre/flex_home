@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['url' => route('real-estate.settings'), 'class' => 'main-setting-form']) !!}
    <div class="max-width-1200">

        <x-core-setting::section
            :title="trans('plugins/real-estate::settings.general')"
            :description="trans('plugins/real-estate::settings.general_description')"
        >
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_square_unit">{{ trans('plugins/real-estate::settings.square_unit') }}</label>
                <div class="ui-select-wrapper">
                    <select class="ui-select" name="real_estate_square_unit" id="real_estate_square_unit">
                        <option value="" @selected(setting('real_estate_square_unit', 'm²') == null)>{{ trans('plugins/real-estate::settings.square_unit_none') }}</option>
                        <option value="m²" @selected(setting('real_estate_square_unit', 'm²') === 'm²')>{{ trans('plugins/real-estate::settings.square_unit_meter') }}</option>
                        <option value="ft2" @selected(setting('real_estate_square_unit', 'm²') === 'ft2')>{{ trans('plugins/real-estate::settings.square_unit_feet') }}</option>
                        <option value="yd2" @selected(setting('real_estate_square_unit', 'm²') === 'yd2')>{{ trans('plugins/real-estate::settings.square_unit_yard') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_display_views_count_in_detail_page">{{ trans('plugins/real-estate::settings.display_views_count_in_detail_page') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_display_views_count_in_detail_page" value="1" @checked(setting('real_estate_display_views_count_in_detail_page', 0) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_display_views_count_in_detail_page" value="0" @checked(setting('real_estate_display_views_count_in_detail_page', 0) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_hide_properties_in_statuses">{{ trans('plugins/real-estate::settings.hide_properties_in_statuses') }}
                </label>
                @foreach(\Botble\RealEstate\Enums\PropertyStatusEnum::labels() as $key => $label)
                    <label class="me-2">
                        <input type="checkbox" name="real_estate_hide_properties_in_statuses[]" value="{{ $key }}" @checked(in_array($key, RealEstateHelper::exceptedPropertyStatuses()))>{{ $label }}
                    </label>
                @endforeach
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_hide_projects_in_statuses">{{ trans('plugins/real-estate::settings.hide_projects_in_statuses') }}
                </label>
                @foreach(\Botble\RealEstate\Enums\ProjectStatusEnum::labels() as $key => $label)
                    <label class="me-2">
                        <input type="checkbox" name="real_estate_hide_projects_in_statuses[]" value="{{ $key }}" @checked(in_array($key, RealEstateHelper::exceptedProjectsStatuses()))>{{ $label }}
                    </label>
                @endforeach
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_enable_review_feature">{{ trans('plugins/real-estate::settings.enable_review_feature') }}</label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_review_feature" value="1" @checked(RealEstateHelper::isEnabledReview() === true)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_review_feature" value="0" @checked(RealEstateHelper::isEnabledReview() === false)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_reviews_per_page">{{ trans('plugins/real-estate::settings.reviews_per_page') }}</label>
                <input type="number" class="form-control" name="real_estate_reviews_per_page" value="{{ setting('real_estate_reviews_per_page', 10) }}">
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field">{{ trans('plugins/real-estate::settings.enable_custom_fields') }}</label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enabled_custom_fields_feature" value="1" @checked(RealEstateHelper::isEnabledCustomFields())>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enabled_custom_fields_feature" value="0" @checked(! RealEstateHelper::isEnabledCustomFields())>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
        </x-core-setting::section>

        <x-core-setting::section
            :title="trans('plugins/real-estate::currency.currencies')"
            :description="trans('plugins/real-estate::currency.setting_description')"
        >
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_convert_money_to_text_enabled">{{ trans('plugins/real-estate::settings.real_estate_convert_money_to_text_enabled') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_convert_money_to_text_enabled"
                           value="1"
                           @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_convert_money_to_text_enabled"
                           value="0"
                           @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_enable_auto_detect_visitor_currency">{{ trans('plugins/real-estate::settings.enable_auto_detect_visitor_currency') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_auto_detect_visitor_currency"
                           value="1"
                           @if (setting('real_estate_enable_auto_detect_visitor_currency', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_auto_detect_visitor_currency"
                           value="0"
                           @if (setting('real_estate_enable_auto_detect_visitor_currency', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_add_space_between_price_and_currency">{{ trans('plugins/real-estate::settings.add_space_between_price_and_currency') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_add_space_between_price_and_currency"
                           value="1"
                           @if (setting('real_estate_add_space_between_price_and_currency', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_add_space_between_price_and_currency"
                           value="0"
                           @if (setting('real_estate_add_space_between_price_and_currency', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-sm-6">
                    <label class="text-title-field" for="real_estate_thousands_separator">{{ trans('plugins/real-estate::settings.thousands_separator') }}</label>
                    <div class="ui-select-wrapper">
                        <select class="ui-select" name="real_estate_thousands_separator" id="real_estate_thousands_separator">
                            <option value="," @if (setting('real_estate_thousands_separator', ',') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                            <option value="." @if (setting('real_estate_thousands_separator', ',') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                            <option value="space" @if (setting('real_estate_thousands_separator', ',') == 'space') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                        </svg>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="text-title-field" for="real_estate_decimal_separator">{{ trans('plugins/real-estate::settings.decimal_separator') }}</label>
                    <div class="ui-select-wrapper">
                        <select class="ui-select" name="real_estate_decimal_separator" id="real_estate_decimal_separator">
                            <option value="." @if (setting('real_estate_decimal_separator', '.') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                            <option value="," @if (setting('real_estate_decimal_separator', '.') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                            <option value="space" @if (setting('real_estate_decimal_separator', '.') == 'space') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                        </svg>
                    </div>
                </div>
            </div>

        <textarea name="currencies"
                  id="currencies"
                  class="hidden">{!! json_encode($currencies) !!}</textarea>
            <textarea name="deleted_currencies"
                      id="deleted_currencies"
                      class="hidden"></textarea>
            <div class="swatches-container">
                <div class="header clearfix">
                    <div class="swatch-item">
                        {{ trans('plugins/real-estate::currency.name') }}
                    </div>
                    <div class="swatch-item">
                        {{ trans('plugins/real-estate::currency.symbol') }}
                    </div>
                    <div class="swatch-item swatch-decimals">
                        {{ trans('plugins/real-estate::currency.number_of_decimals') }}
                    </div>
                    <div class="swatch-item swatch-exchange-rate">
                        {{ trans('plugins/real-estate::currency.exchange_rate') }}
                    </div>
                    <div class="swatch-item swatch-is-prefix-symbol">
                        {{ trans('plugins/real-estate::currency.is_prefix_symbol') }}
                    </div>
                    <div class="swatch-is-default">
                        {{ trans('plugins/real-estate::currency.is_default') }}
                    </div>
                    <div class="remove-item">{{ trans('plugins/real-estate::currency.remove') }}</div>
                </div>
                <ul class="swatches-list">

                </ul>
                <div class="clearfix"></div>
                {!! Form::helper(trans('plugins/real-estate::currency.instruction')) !!}
                <a href="#" class="js-add-new-attribute">
                    {{ trans('plugins/real-estate::currency.new_currency') }}
                </a>
            </div>
        </x-core-setting::section>

        <x-core-setting::section
            :title="trans('plugins/real-estate::settings.title')"
            :description="trans('plugins/real-estate::settings.description')"
        >
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_enabled_login">{{ trans('plugins/real-estate::settings.real_estate_enabled_login') }}</label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enabled_login" value="1" @checked(RealEstateHelper::isLoginEnabled()) class="setting-selection-option" data-target="#auth-settings">
                    {{ trans('core/setting::setting.general.yes') }}
                </label>
                <label>
                    <input type="radio" name="real_estate_enabled_login" value="0" @checked(! RealEstateHelper::isLoginEnabled()) class="setting-selection-option" data-target="#auth-settings">
                    {{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div id="auth-settings" @class(['mb-4 border rounded-top rounded-bottom p-3 bg-light', 'd-none' => ! RealEstateHelper::isLoginEnabled()])>
                <div class="form-group mb-3">
                    <label class="text-title-field" for="real_estate_enabled_register">{{ trans('plugins/real-estate::settings.real_estate_enabled_register') }}</label>
                    <label class="me-2">
                        <input type="radio" name="real_estate_enabled_register" value="1" @checked(RealEstateHelper::isRegisterEnabled())>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label>
                        <input type="radio" name="real_estate_enabled_register" value="0" @checked(! RealEstateHelper::isRegisterEnabled())>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>

                @if (is_plugin_active('captcha'))
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_enable_recaptcha_in_register_page">{{ trans('plugins/real-estate::settings.enable_recaptcha_in_register_page') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_recaptcha_in_register_page"
                                   value="1"
                                   @if (setting('real_estate_enable_recaptcha_in_register_page', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_recaptcha_in_register_page"
                                   value="0"
                                   @if (setting('real_estate_enable_recaptcha_in_register_page', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                        <span class="help-ts">{{ trans('plugins/real-estate::settings.enable_recaptcha_in_register_page_description') }}</span>
                    </div>
                @endif

                <div class="form-group mb-3">
                    <label class="text-title-field"
                           for="verify_account_email">{{ trans('plugins/real-estate::settings.verify_account_email') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="verify_account_email"
                               value="1"
                               @if (setting('verify_account_email', false) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label>
                        <input type="radio" name="verify_account_email"
                               value="0"
                               @if (setting('verify_account_email', false) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>
                <div class="form-group mb-3">
                    <label class="text-title-field"
                           for="real_estate_enable_credits_system">{{ trans('plugins/real-estate::settings.enable_credits_system') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="real_estate_enable_credits_system"
                               value="1"
                               @if (RealEstateHelper::isEnabledCreditsSystem()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="real_estate_enable_credits_system"
                               value="0"
                               @if (!RealEstateHelper::isEnabledCreditsSystem()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>
                <div class="form-group mb-3">
                    <label class="text-title-field"
                           for="enable_post_approval">{{ trans('plugins/real-estate::settings.enable_post_approval') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="enable_post_approval"
                               value="1"
                               @if (setting('enable_post_approval', 1) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label>
                        <input type="radio" name="enable_post_approval"
                               value="0"
                               @if (setting('enable_post_approval', 1) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>

                <x-core-setting::text-input
                    name="real_estate_max_filesize_upload_by_agent"
                    :label="trans('plugins/real-estate::settings.max_upload_filesize')"
                    type="number"
                    :value="RealEstateHelper::maxFilesizeUploadByAgent()"
                    step="0.01"
                    :placeholder="trans('plugins/real-estate::settings.max_upload_filesize_placeholder', ['size' => $maxSize = RealEstateHelper::maxFilesizeUploadByAgent()])"
                />

                <x-core-setting::text-input
                    name="real_estate_max_property_images_upload_by_agent"
                    :label="trans('plugins/real-estate::settings.max_property_images_upload_by_agent')"
                    type="number"
                    :value="RealEstateHelper::maxPropertyImagesUploadByAgent()"
                    step="0.01"
                />
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="property_expired_after_days">{{ trans('plugins/real-estate::settings.property_expired_after_days') }}
                </label>
                <input type="number" class="form-control" name="property_expired_after_days" value="{{ RealEstateHelper::propertyExpiredDays() }}">
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_enable_wishlist">{{ trans('plugins/real-estate::settings.enable_wishlist') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_wishlist"
                           value="1"
                           @if (setting('real_estate_enable_wishlist', 1) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label>
                    <input type="radio" name="real_estate_enable_wishlist"
                           value="0"
                           @if (setting('real_estate_enable_wishlist', 1) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_hide_agency_phone">{{ trans('plugins/real-estate::settings.hide_agency_phone') }}</label>
                <label class="me-2">
                    <input type="radio" name="real_estate_hide_agency_phone" value="1" @checked(setting('real_estate_hide_agency_phone', 0) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_hide_agency_phone" value="0" @checked(setting('real_estate_hide_agency_phone', 0) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_hide_agency_email">{{ trans('plugins/real-estate::settings.hide_agency_email') }}</label>
                <label class="me-2">
                    <input type="radio" name="real_estate_hide_agency_email" value="1" @checked(setting('real_estate_hide_agency_email', 0) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_hide_agency_email" value="0" @checked(setting('real_estate_hide_agency_email', 0) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
        </x-core-setting::section>

        <x-core-setting::section
            :title="trans('plugins/real-estate::settings.invoice_settings')"
            :description="trans('plugins/real-estate::settings.invoice_settings_description')"
        >
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_company_name_for_invoicing">{{ trans('plugins/real-estate::settings.invoicing.company_name') }}</label>
                <input type="text" class="form-control" name="real_estate_company_name_for_invoicing" value="{{ setting('real_estate_company_name_for_invoicing', theme_option('site_title')) }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_company_address_for_invoicing">{{ trans('plugins/real-estate::settings.invoicing.company_address') }}</label>
                <input type="text" class="form-control" name="real_estate_company_address_for_invoicing" value="{{ setting('real_estate_company_address_for_invoicing') }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_company_email_for_invoicing">{{ trans('plugins/real-estate::settings.invoicing.company_email') }}</label>
                <input type="text" class="form-control" name="real_estate_company_email_for_invoicing" value="{{ setting('real_estate_company_email_for_invoicing', get_admin_email()->first()) }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_company_phone_for_invoicing">{{ trans('plugins/real-estate::settings.invoicing.company_phone') }}</label>
                <input type="text" class="form-control" name="real_estate_company_phone_for_invoicing" value="{{ setting('real_estate_company_phone_for_invoicing') }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_company_logo_for_invoicing">{{ trans('plugins/real-estate::settings.invoicing.company_logo') }}</label>
                {!! Form::mediaImage('real_estate_company_logo_for_invoicing', setting('real_estate_company_logo_for_invoicing') ?: theme_option('logo'), ['allow_thumb' => false]) !!}
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_using_custom_font_for_invoice">{{ trans('plugins/real-estate::settings.using_custom_font_for_invoice') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_using_custom_font_for_invoice" value="1" @checked(setting('real_estate_using_custom_font_for_invoice', 0) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label>
                    <input type="radio" name="real_estate_using_custom_font_for_invoice" value="0" @checked(setting('real_estate_using_custom_font_for_invoice', 0) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_invoice_font_family">{{ trans('plugins/real-estate::settings.invoice_font_family') }}</label>
                {!! Form::googleFonts('real_estate_invoice_font_family', setting('real_estate_invoice_font_family')) !!}
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_invoice_support_arabic_language">{{ trans('plugins/real-estate::settings.invoice_support_arabic_language') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_invoice_support_arabic_language" value="1" @checked(setting('real_estate_invoice_support_arabic_language', 0) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label>
                    <input type="radio" name="real_estate_invoice_support_arabic_language" value="0"@checked(setting('real_estate_invoice_support_arabic_language', 0) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="real_estate_enable_invoice_stamp">{{ trans('plugins/real-estate::settings.enable_invoice_stamp') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="real_estate_enable_invoice_stamp" value="1" @checked(setting('real_estate_enable_invoice_stamp', 1) == 1)>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label>
                    <input type="radio" name="real_estate_enable_invoice_stamp" value="0" @checked(setting('real_estate_enable_invoice_stamp', 1) == 0)>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field" for="real_estate_invoice_code_prefix">{{ trans('plugins/real-estate::settings.invoice_code_prefix') }}</label>
                <input type="text" class="form-control" name="real_estate_invoice_code_prefix" value="{{ setting('real_estate_invoice_code_prefix', 'INV-') }}">
            </div>
        </x-core-setting::section>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('plugins/real-estate::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
