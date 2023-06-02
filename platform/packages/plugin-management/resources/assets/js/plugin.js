class PluginManagement {
    init() {
        $(document).on('click', '.btn-trigger-remove-plugin', (event) => {
            event.preventDefault()
            $('#confirm-remove-plugin-button').data('plugin', $(event.currentTarget).data('plugin'))
            $('#remove-plugin-modal').modal('show')
        })

        $(document).on('click', '#confirm-remove-plugin-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.addClass('button-loading')

            $.ajax({
                url: route('plugins.remove', { plugin: _self.data('plugin') }),
                type: 'POST',
                data: { _method: 'DELETE' },
                success: (data) => {
                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        Botble.showSuccess(data.message)
                        window.location.reload()
                    }
                    _self.removeClass('button-loading')
                    $('#remove-plugin-modal').modal('hide')
                },
                error: (data) => {
                    Botble.handleError(data)
                    _self.removeClass('button-loading')
                    $('#remove-plugin-modal').modal('hide')
                },
            })
        })

        $(document).on('click', '.btn-trigger-update-plugin', (event) => {
            event.preventDefault()

            let _self = $(event.currentTarget)
            let uuid = _self.data('uuid')

            _self.addClass('button-loading')
            _self.attr('disabled', true)

            $.ajax({
                url: route('plugins.marketplace.ajax.update', { id: uuid }),
                type: 'POST',
                success: (data) => {
                    if (data.error) {
                        Botble.showError(data.message)

                        _self.removeClass('button-loading')
                        _self.removeAttr('disabled', true)

                        if (data.data && data.data.redirect) {
                            window.location.href
                        }
                    } else {
                        Botble.showSuccess(data.message)
                        setTimeout(() => {
                            window.location.reload()
                        }, 2000)
                    }
                },
                error: (data) => {
                    Botble.handleError(data)
                    _self.removeClass('button-loading')
                    _self.removeAttr('disabled', true)
                },
            })
        })

        $(document).on('click', '.btn-trigger-change-status', async (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.addClass('button-loading')

            const pluginName = _self.data('plugin')

            if (_self.data('status') === 1) {
                await this.activateOrDeactivatePlugin(pluginName)
                return
            }

            $.ajax({
                url: route('plugins.check-requirement', { name: pluginName }),
                type: 'POST',
                success: async (response) => {
                    const { error, data, message } = response
                    if (error) {
                        if (data && data.existing_plugins_on_marketplace) {
                            const $modal = $('#confirm-install-plugin-modal')
                            $modal.find('.modal-body #requirement-message').html(message)
                            $modal.find('input[name="plugin_name"]').val(pluginName)
                            $modal.find('input[name="ids"]').val(data.existing_plugins_on_marketplace)
                            $modal.modal('show')
                            _self.removeClass('button-loading')
                            return
                        }
                        Botble.showError(message)
                    } else {
                        await this.activateOrDeactivatePlugin(pluginName)
                    }
                    _self.removeClass('button-loading')
                },
                error: (error) => {
                    Botble.handleError(error)
                    _self.removeClass('button-loading')
                },
            })
        })

        $(document).on('click', '#confirm-install-plugin-button', async (event) => {
            const _self = $(event.currentTarget)
            _self.addClass('button-loading')

            const $body = _self.parent().parent()

            const pluginName = $body.find('input[name="plugin_name"]').val()
            const pluginIds = $body.find('input[name="ids"]').val()
            const activatedPlugins = []

            for (const pluginId of pluginIds.split(',')) {
                const response = await this.installPlugin(pluginId)
                if (response) {
                    activatedPlugins.push(response.name)
                }
            }

            for (const pluginName of activatedPlugins) {
                await this.activateOrDeactivatePlugin(pluginName, false)
            }

            await this.activateOrDeactivatePlugin(pluginName)

            _self.removeClass('button-loading')

            _self.text(_self.data('text'))
        })

        this.checkUpdate()
    }

    checkUpdate() {
        $.ajax({
            url: route('plugins.marketplace.ajax.check-update'),
            type: 'POST',
            success: (data) => {
                if (!data.data) {
                    return
                }

                Object.keys(data.data).forEach((key) => {
                    const plugin = data.data[key]

                    const element = $('[data-check-update="' + plugin.name + '"]')

                    const $checkVersion = this.checkVersion(element.data('version'), plugin.version)

                    if ($checkVersion) {
                        element.show()
                        element.attr('data-uuid', plugin.id)
                    }
                })
            },
        })
    }

    checkVersion(currentVersion, latestVersion) {
        const current = currentVersion.toString().split('.')
        const latest = latestVersion.toString().split('.')

        const length = Math.max(current.length, latest.length)

        for (let i = 0; i < length; i++) {
            const oldVer = ~~current[i]
            const newVer = ~~latest[i]

            if (newVer > oldVer) {
                return true
            }
        }

        return false
    }

    async activateOrDeactivatePlugin(pluginName, reload = true) {
        await $.ajax({
            url: route('plugins.change.status', { name: pluginName }),
            type: 'POST',
            data: { _method: 'PUT' },
            success: (data) => {
                if (!data.error) {
                    Botble.showSuccess(data.message)
                    if (reload) {
                        $('#plugin-list #app-' + pluginName).load(
                            window.location.href + ' #plugin-list #app-' + pluginName + ' > *'
                        )
                        window.location.reload()
                    }
                    return
                }
                Botble.showError(data.message)
            },
            error: (data) => {
                Botble.handleError(data)
            },
        })
    }

    async installPlugin(id) {
        let data = null

        await $.ajax({
            method: 'POST',
            url: route('plugins.marketplace.ajax.install', { id }),
            success: (response) => (data = response.error ? [] : response.data),
            error: (error) => {
                Botble.handleError(error)
            },
        })

        return data
    }
}

$(document).ready(() => {
    new PluginManagement().init()
})
