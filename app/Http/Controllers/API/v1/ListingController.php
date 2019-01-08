<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;

use App\Services\Listing\Validator\CreateListingRequestValidator;
use App\Services\Listing\Validator\UpdateListingRequestValidator;

use Exception;
use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

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
     * @throws ModelNotFoundException
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
                'message' => $e->getMessage()
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
     * @throws ValidationException
     * @throws JWTException
     * @throws Exception
     * @throws ModelNotFoundException
     * @throws Throwable
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exist.'
            ], 404);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (Throwable $e) {
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
     * URL: /land-for-sale/{id}/update
     * @param Request $request
     * @param int $id
     * @throws ValidationException
     * @throws JWTException
     * @throws ModelNotFoundException
     * @throws Exception
     * @throws Throwable
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->listingRepo->checkPermission($id);
            $data = (new UpdateListingRequestValidator)->attempt($request);
            $listing = $this->listingService->update($data, $id);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exist.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        } catch (Throwable $e) {
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
     * Delete listing
     * METHOD: get
     * URL: /land-for-sale/{id}/delete
     * @param int $id
     * @throws JWTException
     * @throws ModelNotFoundException
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->listingRepo->checkPermission($id);
            $this->listingService->delete($id);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exist.'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Listing successfully deleted.'
        ]);
    }

}
