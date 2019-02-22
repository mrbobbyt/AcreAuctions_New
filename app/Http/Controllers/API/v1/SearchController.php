<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingCollection;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use App\Services\SearchListing\Validator\SearchListingRequestValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected $searchRepo;
    protected $listingRepo;

    public function __construct(
        SearchListingRepositoryContract $searchRepo,
        ListingRepositoryContract $listingRepo
    ) {
        $this->searchRepo = $searchRepo;
        $this->listingRepo = $listingRepo;
    }


    /**
     * METHOD: get
     * URL: /search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $data = (new SearchListingRequestValidator())->attempt($request);

            $result = $this->searchRepo->findListings($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listings' => new ListingCollection($result)
        ]);
    }


    /**
     * METHOD: get
     * URL: /land-for-sale/filters
     * @return JsonResponse
     */
    public function filters(): JsonResponse
    {
        try {
            $states = $this->searchRepo->getStates();

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'states' => $states,
            'property_type' => $this->listingRepo->getPropertyTypes(),
            'sale_types' => $this->listingRepo->getSaleTypes(),
        ]);
    }
}
