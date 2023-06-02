<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PropertyTemplateExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection(): Collection
    {
        $yesNo = ['Yes', 'No'];

        $currency = Currency::query()->inRandomOrder()->value('title');
        $project = Project::query()->inRandomOrder()->value('id');
        $author = Account::query()->inRandomOrder()->value('id');

        $data = [
            '3 Beds Villa Calpe, Alicante',
            'Lavida Plus Office-tel 1 Bedroom',
            'Vinhomes Grand Park Studio 1 Bedroom',
            'The Sun Avenue Office-tel 1 Bedroom',
        ];

        $properties = [];

        foreach ($data as $item) {
            $properties[] = [
                'name' => $item,
                'type' => PropertyTypeEnum::RENT(),
                'description' => null,
                'price' => rand(1000, 100000),
                'number_bedroom' => rand(1, 5),
                'number_bathroom' => rand(1, 5),
                'number_floor' => rand(1, 10),
                'square' => rand(100, 1000),
                'images' => 'properties/1.jpg',
                'author_id' => $author,
                'author_type' => Account::class,
                'currency' => $currency,
                'is_featured' => $yesNo[rand(0, 1)],
                'project_id' => $project,
                'content' => 'content',
                'location' => '8642 Yule Street, Armada CO 80007',
                'longitude' => '-76.72488',
                'latitude' => '43.478881',
                'auto_renew' => $yesNo[rand(0, 1)],
                'country' => null,
                'state' => null,
                'city' => null,
                'expire_date' => Carbon::now()->addDays(61)->toDateString(),
                'never_expired' => $yesNo[rand(0, 1)],
                'period' => PropertyPeriodEnum::MONTH,
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'status' => PropertyStatusEnum::RENTED(),
            ];
        }

        return new Collection($properties);
    }

    public function headings(): array
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'price' => 'Price',
            'number_bedroom' => 'Number bedroom',
            'number_bathroom' => 'Number bathroom',
            'number_floor' => 'Number floor',
            'square' => 'Square',
            'images' => 'Images',
            'author_id' => 'Author',
            'author_type' => 'Author type',
            'currency' => 'Currency',
            'is_featured' => 'Is featured?',
            'project_id' => 'Project',
            'content' => 'Content',
            'location' => 'Location',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'auto_renew' => 'Auto renew',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'expire_date' => 'Expire date',
            'never_expired' => 'Never expired',
            'period' => 'Period',
            'moderation_status' => 'Moderation status',
            'status' => 'Status',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable|max:400',
            'content' => 'nullable|string',
            'type' => 'required|enum:rent,sale',
            'number_bedroom' => 'numeric|min:0|max:10000|nullable',
            'number_bathroom' => 'numeric|min:0|max:10000|nullable',
            'number_floor' => 'numeric|min:0|max:10000|nullable',
            'square' => 'nullable|numeric|min:0',
            'images' => 'nullable|string|multiple',
            'price' => 'numeric|min:0|nullable',
            'latitude' => 'max:20|nullable|regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            'longitude' => 'max:20|nullable|regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            'author_id' => 'nullable|Author id',
            'is_featured' => 'required|boolean (Yes or No)',
            'period' => 'nullable|enum:day,month,year',
            'location' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'auto_renew' => 'required|boolean (Yes or No)',
            'expire_date' => 'nullable|date_format:Y-m-d',
            'never_expired' => 'nullable|date_format:Y-m-d',
            'moderation_status' => 'required|enum:approved,pending,rejected (default: pending)',
            'status' => 'required|enum:not_available,pre_sale,selling,sold,renting,rented,building',
        ];
    }
}
