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
     * URL: /api/land-for-sale/{slug}
     * @param string $slug
     * @throws Exception
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $listing = $this->listingRepo->findBySlug($slug);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
    }


    /**
     * Create Seller
     * METHOD: post
     * URL: /api/land-for-sale/create
     * @param Request $request
     * @throws ValidationException
     * @throws JWTException
     * @throws Exception
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
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'status' => 'Success',
            'listing' => ListingResource::make($listing)
        ]);
    }

}
