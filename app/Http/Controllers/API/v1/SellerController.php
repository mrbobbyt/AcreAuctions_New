<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\SellerResource;
use App\Repositories\Seller\SellerRepository;
use App\Services\Seller\Contracts\SellerServiceContract;
use App\Services\Seller\Validators\SellerCleateRequestValidator;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

class SellerController extends Controller
{

    protected $sellerRepo;
    protected $sellerService;

    public function __construct(SellerRepository $sellerRepo, SellerServiceContract $sellerService)
    {
        $this->sellerRepo = $sellerRepo;
        $this->sellerService = $sellerService;
    }


    /**
     * View company-seller
     *
     * METHOD: get
     * URL: /api/seller/{slug}
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $seller = $this->sellerRepo->findBySlug($slug);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'user' => SellerResource::make($seller)
        ]);
    }


    /**
     * Create Seller
     *
     * METHOD: post
     * URL: /api/seller/create
     *
     * @param Request $request
     * @throws ValidationException
     * @throws Throwable
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $data = (new SellerCleateRequestValidator)->attempt($request);
            $seller = $this->sellerService->create($data['body']);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, the seller could not register.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => SellerResource::make($seller)
        ]);
    }


    public function uploadLogo()
    {
        //
    }
}
