class SlugBoxManagement {
    init() {
        let $slugBox = $(document).find('#edit-slug-box')
        let $slugInput = $(document).find('#editable-post-name')
        let $slugId = $(document).find('#slug_id')
        let $changeSlug = $(document).find('#change_slug')
        let $currentSlug = $(document).find('#current-slug')
        let $permalink = $(document).find('#sample-permalink')

        $(document).on('click', '#change_slug', (event) => {
            $(document).find('.default-slug').unwrap()
            $slugInput.html(
                '<input type="text" id="new-post-slug" class="form-control" value="' +
                    $slugInput.text() +
                    '" autocomplete="off">'
            )
            $slugBox.find('.cancel').show()
            $slugBox.find('.save').show()
            $(event.currentTarget).hide()
        })

        $(document).on('click', '#edit-slug-box .cancel', () => {
            let currentSlug = $currentSlug.val()

            $permalink.html(
                '<a class="permalink" href="' +
                    $slugId.data('view') +
                    currentSlug.replace('/', '') +
                    '">' +
                    $permalink.html() +
                    '</a>'
            )
            $slugInput.text(currentSlug)
            $slugBox.find('.cancel').hide()
            $slugBox.find('.save').hide()
            $changeSlug.show()
        })

        let createSlug = (name, id, exist) => {
            $.ajax({
                url: $slugId.data('url'),
                type: 'POST',
                data: {
                    value: name,
                    slug_id: id,
                    model: $(document).find('input[name=model]').val(),
                },
                success: (data) => {
                    if (exist) {
                        $permalink.find('.permalink').prop('href', $slugId.data('view') + data.replace('/', ''))
                    } else {
                        $permalink.html(
                            '<a class="permalink" target="_blank" href="' +
                                $slugId.data('view') +
                                data.replace('/', '') +
                                '">' +
                                $permalink.html() +
                                '</a>'
                        )
                    }

                    $(document).find('.page-url-seo p').text($slugId.data('view') + data.replace('/', ''))

                    $slugInput.text(data)
                    $currentSlug.val(data)
                    $slugBox.find('.cancel').hide()
                    $slugBox.find('.save').hide()
                    $changeSlug.show()
                    $slugBox.removeClass('hidden')
                },
                error: (data) => {
                    Botble.handleError(data)
                },
            })
        }

        $(document).on('click', '#edit-slug-box .save', () => {
            let $slugField = $(document).find('#new-post-slug')
            let name = $slugField.val()
            let id = $slugId.data('id')
            if (id == null) {
                id = 0
            }
            if (name != null && name !== '') {
                createSlug(name, id, false)
            } else {
                $slugField.closest('.form-group').addClass('has-error')
            }
        })

        $(document).on('blur', '#' + $slugBox.data('field-name'), (e) => {
            if ($slugBox.hasClass('hidden')) {
                let value = $(e.currentTarget).val()

                if (value !== null && value !== '') {
                    createSlug(value, 0, true)
                }
            }
        })
    }
}

$(() => {
    new SlugBoxManagement().init()

    document.addEventListener('core-init-resources', function() {
        new SlugBoxManagement().init()
    })
})
