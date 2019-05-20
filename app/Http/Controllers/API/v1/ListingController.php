<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Services\Social\Contracts\ShareServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;
use App\Repositories\Listing\Exceptions\ListingNotFoundException;

use App\Services\Listing\Validator\CreateListingRequestValidator;
use App\Services\Listing\Validator\UpdateListingRequestValidator;

use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Services\Listing\Exceptions\ListingAlreadyExistsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListingController extends Controller
{
    protected $listingService;
    protected $listingRepo;
    protected $shareService;

    public function __construct(
        ListingServiceContract $listingService,
        ListingRepositoryContract $listingRepo,
        ShareServiceContract $shareService
    )
    {
        $this->listingService = $listingService;
        $this->listingRepo = $listingRepo;
        $this->shareService = $shareService;
    }


    /**
     * METHOD: get
     * URL: /land-for-sale/{slug}
     * @param string $slug
     * @return Response
     */
    public function view(string $slug): Response
    {
        try {
            $listing = $this->listingRepo->findBySlug($slug);
            $shareLinks = $this->shareService->shareSocials(request()->url(), $listing->title);

        } catch (ListingNotFoundException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['listing' => ListingResource::make($listing), 'shareLinks' => $shareLinks]);
    }


    /**
     * Create Listing
     * METHOD: post
     * URL: /land-for-sale/create
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = (new CreateListingRequestValidator)->attempt($request);
            $listing = $this->listingService->create($data);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (TokenExpiredException | TokenInvalidException | ListingAlreadyExistsException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Seller not exist.'], Response::HTTP_NOT_FOUND);
        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['listing' => ListingResource::make($listing)]);
    }


    /**
     * Update Listing
     * METHOD: post
     * URL: /land-for-sale/{id}
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        try {
            $data = (new UpdateListingRequestValidator)->attempt($request);
            $listing = $this->listingService->update($data, $id);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (ListingAlreadyExistsException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Listing not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'Listing update error.'], Response::HTTP_BAD_REQUEST);
        }

        return \response(['listing' => ListingResource::make($listing)]);
    }


    /**
     * Delete listing
     * METHOD: delete
     * URL: /land-for-sale/{id}
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        try {
            $this->listingService->delete($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Listing not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'Listing delete error.'], Response::HTTP_BAD_REQUEST);
        }

        return \response(['message' => 'Listing successfully deleted.']);
    }


    /**
     * Return properties for create listing
     * METHOD: get
     * URL: /land-for-sale/properties
     * @return Response
     */
    public function getAvailableProperties(): Response
    {
        return \response([
            'property_type' => $this->listingRepo->getPropertyTypes(),
            'sale_types' => $this->listingRepo->getSaleTypes(),
            'road_access' => $this->listingRepo->getRoadAccess(),
            'utilities' => $this->listingRepo->getUtilities(),
            'zoning' => $this->listingRepo->getZoning(),
            'listing_status' => $this->listingRepo->getListingStatus(),
        ]);
    }

}
