<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return app(ProjectInterface::class)
            ->getModel()
            ->with([
                'investor',
                'categories',
                'features',
                'facilities',
                'customFields',
            ])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Content',
            'Images',
            'Location',
            'Investor ID',
            'Number Block',
            'Number Floor',
            'Number Flat',
            'Is Featured?',
            'Date Finish',
            'Date Sell',
            'Price from',
            'Price to',
            'Currency',
            'City',
            'Country',
            'State',
            'Author',
            'Author Type',
            'Longitude',
            'Latitude',
            'Status',
            'Categories',
            'Features',
            'Facilities',
            'Custom Fields',
        ];
    }

    public function map($row): array
    {
        $facilities = $row->facilities->pluck('pivot.distance', 'name')->all();
        array_walk(
            $facilities,
            function (&$v, $k) {
                $v = $k . ':' . $v;
            }
        );

        return [
            $row->id,
            $row->name,
            $row->description,
            $row->content,
            implode(',', $row->images),
            $row->location,
            $row->investor_id,
            $row->number_block,
            $row->number_floor,
            $row->number_flat,
            $row->is_featured,
            $row->date_finish,
            $row->date_sell,
            $row->price_from,
            $row->price_to,
            $row->currency_id,
            $row->city_id,
            $row->country_id,
            $row->state_id,
            $row->author_id,
            $row->author_type,
            $row->longitude,
            $row->latitude,
            $row->status,
            implode(',', $row->categories->pluck('name')->all()),
            implode(',', $row->features->pluck('name')->all()),
            implode(',', $facilities),
            implode(',', $row->customFields->pluck('id')->all()),
        ];
    }
}
