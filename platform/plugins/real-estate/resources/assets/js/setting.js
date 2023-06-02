$(document).ready(function () {
    $('input.setting-selection-option').each(function (index, el) {
        const $settingContentContainer = $($(el).data('target'))
        $(el).on('change', function () {
            if ($(el).val() === '1') {
                $settingContentContainer.removeClass('d-none')
                Botble.initResources()
            } else {
                $settingContentContainer.addClass('d-none')
            }
        })
    })
})
