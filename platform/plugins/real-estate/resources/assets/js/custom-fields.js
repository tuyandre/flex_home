$(document).ready(() => {
    class JobCustomFields {
        $jobCustomFieldsWrap = $('.job-custom-fields-wrap')

        $customFieldList = $('#custom-field-list')

        options = window.jobCustomFields


        init() {
            this.actions()

            const availableCustomFields = this.options.customFields

            availableCustomFields.forEach((item) => {
                const index = this.$customFieldList.find('.row').length
                const options = {
                    id: item.id,
                    name: item.name,
                    value: item.value,
                }

                if (item.custom_field_id && item.custom_field.type === 'dropdown') {
                    this.generateDropdownTemplate({
                        ...options,
                        custom_field_id: item.custom_field_id,
                        selectOptions: item.custom_field.options
                    }, index, true)
                } else {
                    this.generateTextTemplate({
                        ...options,
                        custom_field_id: item.custom_field_id,
                    }, index, !!item.custom_field_id)
                }
            })
        }

        actions() {
            this.$jobCustomFieldsWrap
                .on('click', '#add-new', async () => {
                    const id = $('#custom-field-id').val()

                    if (id === '') {
                        this.generateTextTemplate({}, this.$customFieldList.find('.row').length)
                    } else {
                        const data = (await this.addFromGlobal(id)).data
                        const index = this.$customFieldList.find('.row').length

                        if (data.type === 'dropdown') {
                            this.generateDropdownTemplate({
                                custom_field_id: data.id,
                                name: data.name,
                                selectOptions: data.options,
                            }, index, true)
                        } else if (data.type === 'text') {
                            this.generateTextTemplate({
                                custom_field_id: data.id,
                                name: data.name,
                            }, index, true)
                        }
                    }

                    $('#addNewCustomFieldModal').modal('toggle');
                })
                .on('click', '.remove-custom-field', function () {
                    $(this).parent().parent().parent().remove()
                })
        }

        async addFromGlobal(id) {
            return await (await fetch(`${this.options.ajax}?id=${id}`)).json()
        }

        generateTextTemplate(options, index, isGlobalField = false) {
            const template = $('#custom-field-template')
                .html()
                .replace(/__id__/g, options.id || '')
                .replace(/__custom_field_id__/g, options.custom_field_id || '')
                .replace(/__name__/g, options.name || '')
                .replace(/__value__/g, options.value || '')
                .replace(/__index__/g, index)
                .replace(/__custom_field_input_class__/g, isGlobalField ? 'form-control-disabled' : '')

            this.$customFieldList.append(template)

            if (options.custom_field_id) {
                this.$customFieldList.find(`[data-index="${index}"] .custom-field-name`).prop('readonly', true)
            }
        }

        generateDropdownTemplate(options, index, isGlobalField = false) {
            let select = '';
            $.each(options.selectOptions, function (key, option) {
                select += `<option value="${option.value}" ${options.value === option.value ? 'selected' : ''}>${option.label}</option>`
            })

            let template = $('#custom-field-dropdown-template')
                .html()
                .replace(/__id__/g, options.id || '')
                .replace(/__custom_field_id__/g, options.custom_field_id || '')
                .replace(/__name__/g, options.name || '')
                .replace(/__index__/g, index)
                .replace(/__selectOptions__/g, select)
                .replace(/__custom_field_input_class__/g, isGlobalField ? 'form-control-disabled' : '')

            this.$customFieldList.append(template)

            if (options.custom_field_id) {
                this.$customFieldList.find(`[data-index="${index}"] .custom-field-name`).prop('readonly', true)
            }
        }
    }

    new JobCustomFields().init()
})
