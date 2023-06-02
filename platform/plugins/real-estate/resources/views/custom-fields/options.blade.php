<div class="col-md-12" id="custom-field-options">
    <table class="table table-bordered setting-option">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">{{ trans('plugins/real-estate::custom-fields.option.label') }}</th>
            <th scope="col" colspan="2">{{ trans('plugins/real-estate::custom-fields.option.value') }}</th>
        </tr>
        </thead>
        <tbody class="option-sortable">
        <input type="hidden" name="is_global" value="1">
        @if ($options->count())
            @foreach ($options as $key => $value)
                <tr class="option-row ui-state-default" data-index="{{ $value->id }}">
                    <input type="hidden" name="options[{{ $key }}][id]" value="{{ $value->id }}">
                    <input type="hidden" name="options[{{ $key }}][order]" value="{{ $value->order !== 999 ? $value->order : $key }}">
                    <td class="text-center">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td>
                        <input type="text" class="form-control option-label" name="options[{{ $key }}][label]" value="{{ $value->label }}" placeholder="{{ trans('plugins/real-estate::custom-fields.option.label') }}"/>
                    </td>
                    <td>
                        <input type="text" class="form-control option-value" name="options[{{ $key }}][value]" value="{{ $value->value }}" placeholder="{{ trans('plugins/real-estate::custom-fields.option.value') }}"/>
                    </td>
                    <td style="width: 50px">
                        <button type="button" class="btn btn-default remove-row" data-index="0">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="option-row" data-index="0">
                <td class="text-center">
                    <i class="fa fa-sort"></i>
                </td>
                <td>
                    <input type="text" class="form-control option-label" name="options[0][label]" placeholder="{{ trans('plugins/real-estate::custom-fields.option.label') }}"/>
                </td>
                <td>
                    <input type="text" class="form-control option-value" name="options[0][value]" placeholder="{{ trans('plugins/real-estate::custom-fields.option.value') }}"/>
                </td>
                <td style="width: 50px">
                    <button type="button" class="btn btn-default remove-row" data-index="0">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @endif
        </tbody>
    </table>

    <button type="button" class="btn btn-info mt-3" id="add-new-row">{{ trans('plugins/real-estate::custom-fields.option.add_row') }}</button>
</div>
