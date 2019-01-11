<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\SearchListing\SearchListingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    protected $searchRepo;

    public function __construct(SearchListingRepository $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }

    public function search(Request $request)
    {
        $geoParams = $request->only('state', 'acreage');
        $priceParams = $request->only('price');

        if ($geoParams || $priceParams) {
            $result = $this->searchRepo->findByParams($geoParams, $priceParams);
        } else {
            $result = $this->searchRepo->findAll();
        }

        return response()->json([
            'status' => 'Success',
            'listing' => $result
        ]);
    }
}
