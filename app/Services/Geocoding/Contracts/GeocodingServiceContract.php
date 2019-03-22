<?php
declare(strict_types = 1);

namespace App\Services\Geocoding\Contracts;

interface GeocodingServiceContract
{
    /**
     * Make seller verified
     * @param float $lat
     * @param float $lng
     * @return object
     */
    public function reverse(float $lat, float $lng);
}
