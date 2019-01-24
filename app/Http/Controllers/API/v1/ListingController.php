<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;

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

    public function __construct(ListingServiceContract $listingService, ListingRepositoryContract $listingRepo)
    {
        $this->listingService = $listingService;
        $this->listingRepo = $listingRepo;
    }


    /**
     * METHOD: get
     * URL: /land-for-sale/{slug}
     * @param string $slug
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $listing = $this->listingRepo->findBySlug($slug);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exist.'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing show error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
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
                'message' => 'Listing create error.'
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
     * URL: /land-for-sale/{id}/update
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
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
    }


    /**
     * Delete listing
     * METHOD: get
     * URL: /land-for-sale/{id}/delete
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
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Listing successfully deleted.'
        ]);
    }


    /**
     * Return properties for create listing
     * METHOD: get
     * URL: /land-for-sale/create
     * @return JsonResponse
     */
    public function createWithProperties(): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'property_type' => $this->listingRepo->getPropertyTypes(),
            'sale_types' => $this->listingRepo->getSaleTypes(),
            'road_access' => $this->listingRepo->getRoadAccess(),
            'utilities' => $this->listingRepo->getUtilities(),
            'zoning' => $this->listingRepo->getZoning()
        ]);
    }

}
