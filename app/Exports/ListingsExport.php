<?php

namespace App\Exports;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListingsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $id;

    public function __construct(array $id)
    {
        $this->id = $id;
    }


    /**
     * @return Builder
     */
    public function query()
    {
        return Listing::query()->whereIn('id', $this->id);
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID', 'Inner listing id', 'APN', 'Title', 'Description', 'Is featured', 'Status',
            'Seller ID', 'Seller title',
            'Utilities', 'Zoning', 'Zoning description', 'Property type', 'Subdivision', 'Gallery',
            'Acreage', 'State', 'County', 'City', 'Address', 'Zip', 'Road access', 'Longitude', 'Latitude',
            'Price', 'Sale type', 'Monthly payment', 'Processing fee', 'Financial term', 'Taxes',
            'Docs', 'Links', 'Videos'
        ];
    }


    /**
     * @param mixed $user
     * @return array
     */
    public function map($listing): array
    {
        return [
            $listing->id,
            $listing->inner_listing_id,
            $listing->apn,
            $listing->title,
            $listing->description,
            $listing->is_featured,
            $listing->getStatus->name ?? null,
            $listing->seller->id,
            $listing->seller->title,
            $listing->getUtilities->name ?? null,
            $listing->getZoning->name ?? null,
            $listing->zoning_desc,
            $listing->getPropertyType->name ?? null,

            $listing->subdivision ?? null,
            $listing->gallery,

            $listing->geo->acreage,
            $listing->geo->state,
            $listing->geo->county,
            $listing->geo->city,
            $listing->geo->address,
            $listing->geo->zip,
            $listing->geo->getRoadAccess->name ?? null,
            $listing->geo->longitude,
            $listing->geo->latitude,

            $listing->price->price,
            $listing->price->getSaleType->name ?? null,
            $listing->price->monthly_payment,
            $listing->price->processing_fee,
            $listing->price->percentage_rate,
            $listing->price->financial_term,
            $listing->price->taxes,

            $listing->docs,
            $listing->links,
            $listing->videos,
        ];
    }
}
