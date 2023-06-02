<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Investor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectTemplateExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection(): Collection
    {
        $yesNo = ['Yes', 'No'];

        $investor = Investor::query()->inRandomOrder()->value('id');
        $currency = Currency::query()->inRandomOrder()->value('title');
        $author = Account::query()->inRandomOrder()->value('id');

        $data = [
            'Walnut Park Apartments',
            'Sunshine Wonder Villas',
            'Diamond Island',
            'The Nassim',
        ];

        $projects = [];

        foreach ($data as $item) {
            $projects[] = [
                'name' => $item,
                'description' => null,
                'content' => 'Content',
                'images' => 'projects/1.png',
                'location' => '300 Goyette Overpass Lake Kailyn, DC 19522',
                'investor_id' => $investor,
                'number_block' => rand(1, 10),
                'number_floor' => rand(1, 50),
                'number_flat' => rand(100, 5000),
                'is_featured' => $yesNo[rand(0, 1)],
                'date_finish' => Carbon::now()->addDays(61)->toDateString(),
                'date_sell' => Carbon::now()->subMonths(24)->toDateString(),
                'price_from' => rand(100, 1000),
                'price_to' => rand(1000, 10000),
                'currency' => $currency,
                'city' => null,
                'country' => null,
                'state' => null,
                'author_id' => $author,
                'author_type' => Account::class,
                'longitude' => '-76.72488',
                'latitude' => '43.478881',
                'status' => ProjectStatusEnum::SELLING,
                'categories' => 'Apartment,House,Villa,Land,Condo',
                'features' => 'Wifi,Parking,Garden,Security,Fitness center,Laundry Room,Pets Allow',
                'facilities' => 'Hospital:13km,Super Market:2km,School:3km',
                'custom_fields' => '1',

            ];
        }

        return new Collection($projects);
    }

    public function headings(): array
    {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'content' => 'Content',
            'images' => 'Images',
            'location' => 'Location',
            'investor_id' => 'Investor ID',
            'number_block' => 'Number block',
            'number_floor' => 'Number floor',
            'number_flat' => 'Number flat',
            'is_featured' => 'Is featured?',
            'date_finish' => 'Date finish',
            'date_sell' => 'Date sell',
            'price_from' => 'Price from',
            'price_to' => 'Price to',
            'currency' => 'Currency',
            'city' => 'City',
            'country' => 'Country',
            'state' => 'State',
            'author_id' => 'Author ID',
            'author_type' => 'Author Type',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
            'categories' => 'Categories',
            'features' => 'Features',
            'facilities' => 'Facilities',
            'custom_fields' => 'Custom Fields',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable|max:400',
            'content' => 'required|string',
            'images' => 'nullable|string|multiple',
            'location' => 'nullable|string',
            'investor_id' => 'nullable|Investor id',
            'number_block' => 'numeric|min:0|max:10000|nullable',
            'number_floor' => 'numeric|min:0|max:10000|nullable',
            'number_flat' => 'numeric|min:0|max:10000|nullable',
            'is_featured' => 'required|boolean (Yes or No)',
            'date_finish' => 'nullable|date_format:Y-m-d',
            'date_sell' => 'nullable|date_format:Y-m-d',
            'price_from' => 'numeric|min:0|nullable',
            'price_to' => 'numeric|min:0|nullable',
            'currency' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'author_id' => 'nullable|Author id',
            'author_type' => 'nullable|string',
            'longitude' => 'max:20|nullable|regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            'latitude' => 'max:20|nullable|regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            'status' => 'required|enum:not_available,pre_sale,selling,sold,building',
            'categories' => 'nullable|string',
            'features' => 'nullable|string',
            'facilities' => 'nullable|string',
            'custom_fields' => 'nullable|string',
        ];
    }
}
