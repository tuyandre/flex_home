$(() => {

    let isExporting = false

    $(document).on('click', '.btn-export-data', function (event) {
        event.preventDefault()

        if (isExporting) {
            return
        }

        const $currenTarget = $(event.currentTarget)
        const $content = $currenTarget.html()

        $.ajax({
            url: $currenTarget.attr('href'),
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: () => {
                $currenTarget.prop('disabled', true).addClass('button-loading')
                isExporting = true
            },
            success: data => {
                const a = document.createElement('a')
                const url = window.URL.createObjectURL(data)
                a.href = url
                a.download = $currenTarget.data('filename')
                document.body.append(a)
                a.click()
                a.remove()
                window.URL.revokeObjectURL(url)
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                setTimeout(() => {
                    $currenTarget.prop('disabled', false).removeClass('button-loading')
                    isExporting = false
                }, 500)
            }
        })
    })
})
