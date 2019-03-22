<?php

declare(strict_types = 1);

namespace App\Services\Geocoding;

use App\Services\Geocoding\Contracts\GeocodingServiceContract;
use Geocoder\Model\Coordinates;
use Geocoder\Query\ReverseQuery;

class GeocodingService implements GeocodingServiceContract
{
    public function reverse(float $lat, float $lng)
    {
        $coordinates = new Coordinates($lat, $lng);

        return app('geocoder')
            ->reverseQuery(ReverseQuery::create($coordinates))
            ->get()
            ->first()
            ->toArray();
    }
}
