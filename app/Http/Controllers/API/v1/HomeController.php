<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

class HomeController extends Controller
{
    protected $searchRepo;

    public function __construct(SearchListingRepositoryContract $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }


    /**
     * METHOD: get
     * URL: /home/featured
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        try {
            $result = $this->searchRepo->findFeaturedListings();

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listings' => ListingResource::collection($result)
        ]);
    }
}
