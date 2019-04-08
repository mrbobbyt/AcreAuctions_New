<?php

namespace App\Http\Controllers\API\v1;

use App\Services\Geocoding\GeocodingService;
use App\Services\Geocoding\Validators\ReverseGeocodingValidateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class GeocodingController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reverseGeocoding(Request $request)
    {
        try {
            $data = (new ReverseGeocodingValidateRequest())->attempt($request);
            $coordinates = explode(',', $data['addressByCoords']);
            $lat = trim($coordinates[0]);
            $lng = trim($coordinates[1]);
            $address = $this->geocodingService->reverse($lat, $lng);
            return \response($address);
        } catch (\Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }
    }
}
