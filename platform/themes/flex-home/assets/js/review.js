import {handleError, showSuccess} from './utils'

class Review {
    currentPage = 1

    constructor() {
        $('#select-star').barrating({
            theme: 'css-stars',
        })

        this.refresh()

        $(document)
            .on('submit', '.review-form', (e) => {
                e.preventDefault()

                this.submit($(e.currentTarget))
            })
            .on('click', '#pagination ul li a', (e) => {
                e.preventDefault()

                const url = new URL(e.target.href)
                this.currentPage = url.searchParams.get('page') || 1

                this.refresh()

                const $reviewList = $(document).find('.reviews-list')
                $('html, body').animate({
                    scrollTop: $reviewList.offset().top - 220
                }, 0)
            })
    }

    async refresh() {
        const $reviewList = $(document).find('.reviews-list')

        $reviewList.addClass('animate-pulse')

        const url = `${$reviewList.data('url')}&page=${this.currentPage}`

        $.get(url, (response) => {
            $reviewList.html(response.data)
            $reviewList.removeClass('animate-pulse')
        })
    }

    submit(element) {
        const data = element.serializeArray()

        $.ajax({
            method: 'post',
            url: element.prop('action'),
            data: data,
            beforeSend: () => {
                element.find('button').prop('disabled', true).addClass('button-loading')
            },
            success: (response) => {
                $(document).find('.reviews-count').html(response.data.count)
                element.find('textarea').prop('disabled', true)
                element.find('button').prop('disabled', true)
                this.currentPage = 1
                this.refresh()
                showSuccess(response.data.message)
            },
            error: (response) => {
                handleError(response)
                element.find('button').prop('disabled', false)
            },
            complete: () => {
                if (typeof refreshRecaptcha !== 'undefined') {
                    refreshRecaptcha()
                }

                element.find('button').removeClass('button-loading')
                element.find('textarea').val('').trigger('change')
                $('#select-star').barrating('set', 5)
            },
        })
    }
}

$(document).ready(() => {
    new Review()
})
