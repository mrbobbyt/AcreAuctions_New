<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Models\ListingStatus;
use App\Services\Social\Contracts\ShareServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

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
    ) {
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

            if ($listing->status !== ListingStatus::TYPE_AVAILABLE) {
                return response(['message' => 'Listing not found.'], Response::HTTP_NOT_FOUND);
            }

            $shareLinks = $this->shareService->shareSocials(request()->url(), $listing->title);

            return response([
                'listing' => ListingResource::make($listing),
                'shareLinks' => $shareLinks,
            ]);
        } catch (ListingNotFoundException $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }
    }


    /**
     * Create Listing
     * METHOD: post
     * URL: /land-for-sale/create
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $data = (new CreateListingRequestValidator)->attempt($request);
            $listing = $this->listingService->create($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (TokenExpiredException | TokenInvalidException | ListingAlreadyExistsException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
    }


    /**
     * Update Listing
     * METHOD: post
     * URL: /land-for-sale/{id}
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = (new UpdateListingRequestValidator)->attempt($request);
            $listing = $this->listingService->update($data, $id);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (ListingAlreadyExistsException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exist.'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing update error.'
            ], 400);
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
    }


    /**
     * Delete listing
     * METHOD: delete
     * URL: /land-for-sale/{id}
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->listingService->delete($id);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exist.'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing delete error.'
            ], 400);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Listing successfully deleted.'
        ]);
    }


    /**
     * Return properties for create listing
     * METHOD: get
     * URL: /land-for-sale/properties
     * @return JsonResponse
     */
    public function getAvailableProperties(): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'property_type' => $this->listingRepo->getPropertyTypes(),
            'sale_types' => $this->listingRepo->getSaleTypes(),
            'road_access' => $this->listingRepo->getRoadAccess(),
            'utilities' => $this->listingRepo->getUtilities(),
            'zoning' => $this->listingRepo->getZoning(),
            'listing_status' => $this->listingRepo->getListingStatus()
        ]);
    }

}
