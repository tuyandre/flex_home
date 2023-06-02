class ProjectImport {
    isDownloading = false

    $wrapper = $('.project-import')

    constructor() {
        this.$wrapper
            .on('submit', '#project-import-form', (event) => {
                this.submit(event)
            })
            .on('click', '.download-template', (event) => {
                this.download(event)
            })
    }

    submit(event) {
        event.preventDefault()

        const $form = $(event.currentTarget)
        const formData = new FormData($form.get(0))
        const $button = $form.find('button[type=submit]')
        const $failuresList = this.$wrapper.find('#failures-list')
        const $alert = this.$wrapper.find('.alert')

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: () => {
                $button.prop('disabled', true).addClass('button-loading')
                $alert.hide()
                $failuresList.hide()
                $failuresList.find('tbody').html('')
            },
            success: (data) => {
                $alert.show()

                if (data.error) {
                    Botble.showError(data.message)

                    let result = ''
                    _.map(data.data, (item) => {
                        result +=
                            `<tr>
                            <td>${item.row}</td>
                            <td>${item.attribute}</td>
                            <td>${item.errors.join(', ')}</td>
                        </tr>`
                    })

                    $failuresList.show()
                    $failuresList.find('tbody').html(result)

                    $alert.removeClass('alert-success').addClass('alert-danger').html(data.message)
                } else {
                    $alert.removeClass('alert-danger').addClass('alert-success').html(data.data.message)
                    Botble.showSuccess(data.message)
                }

                $form.get(0).reset()
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                $button.prop('disabled', false)
                $button.removeClass('button-loading')
            }
        })
    }

    download(event) {
        event.preventDefault()

        if (this.isDownloading) {
            return
        }

        const $this = $(event.currentTarget)
        const extension = $this.data('extension')
        const $content = $this.html()

        $.ajax({
            url: $this.data('url'),
            method: 'POST',
            data: {extension},
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: () => {
                $this.html($this.data('downloading'))
                $this.addClass('text-secondary')
                this.isDownloading = true
            },
            success: (data) => {
                const anchor = document.createElement('a')
                const url = window.URL.createObjectURL(data)
                anchor.href = url
                anchor.download = $this.data('filename')
                document.body.append(anchor)
                anchor.click()
                anchor.remove()
                window.URL.revokeObjectURL(url)
            },
            error: data => {
                Botble.handleError(data)
            },
            complete: () => {
                setTimeout(() => {
                    $this.html($content)
                    $this.removeClass('text-secondary')
                    this.isDownloading = false
                }, 500)
            }
        })
    }
}

$(() => new ProjectImport())
