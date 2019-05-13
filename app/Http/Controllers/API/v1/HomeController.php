<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Http\Response;
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
     * @return Response
     */
    public function featured(): Response
    {
        try {
            $result = $this->searchRepo->findFeaturedListings();

        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['listings' => ListingResource::collection($result)]);
    }
}
