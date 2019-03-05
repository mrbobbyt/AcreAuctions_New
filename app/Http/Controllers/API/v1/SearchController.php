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
use Illuminate\Http\Response;

class SearchController extends Controller
{
    protected $searchRepo;
    protected $listingRepo;
    protected $searchListingRequestValidator;

    public function __construct(
        SearchListingRepositoryContract $searchRepo,
        ListingRepositoryContract $listingRepo,
        SearchListingRequestValidator $searchListingRequestValidator
    ) {
        $this->searchRepo = $searchRepo;
        $this->listingRepo = $listingRepo;
        $this->searchListingRequestValidator = $searchListingRequestValidator;
    }


    /**
     * METHOD: get
     * URL: /search
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        try {
            $searchData = $this->searchListingRequestValidator->attempt($request);
            $result = $this->searchRepo->findListings($searchData);
        } catch (ValidationException $e) {
            return response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response(['listings' => new ListingCollection($result)]);
    }


    /**
     * METHOD: get
     * URL: /land-for-sale/filters
     * @return Response
     */
    public function getFilters(): Response
    {
        try {
            $states = $this->searchRepo->getStates();
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response([
            'states' => $states,
            'property_type' => $this->listingRepo->getPropertyTypes(),
            'sale_types' => $this->listingRepo->getSaleTypes(),
        ]);
    }
}
