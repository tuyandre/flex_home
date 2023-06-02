<?php

namespace Botble\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkImportRequest extends FormRequest
{
    public function rules(): array
    {
        $mimes = implode(',', config('plugins.real-estate.general.bulk-import.mime_types'));

        return [
            'file' => 'required|file|mimetypes:' . $mimes,
        ];
    }
}
