'use strict';

$(document).ready(function () {
    $(document).on('click', '.btn-duplicate-property', function (event) {
        event.preventDefault()

        const action = $(this).data('action')

        $.ajax({
            url: action,
            method: 'POST',
            beforeSend: () => {
                $(this).prop('disabled', true).addClass('button-loading')
            },
            success: (response) => {
                if (!response.error) {
                    Botble.showSuccess(response.message)
                    setTimeout(function () {
                        window.location.href = response.data.url
                    }, 500);
                } else {
                    Botble.showError(response.message)
                }
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                $(this).prop('disabled', false)
                $(this).removeClass('button-loading')
            }
        })
    })
})
