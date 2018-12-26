<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;

use App\Services\Seller\Contracts\SellerServiceContract;
use App\Repositories\Seller\SellerRepository;

use App\Services\Seller\Validators\CreateSellerRequestValidator;
use App\Services\Seller\Validators\UpdateSellerRequestValidator;

use Illuminate\Validation\ValidationException;
use Exception;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

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
     * METHOD: get
     * URL: /api/seller/{id}
     * @param string $slug
     * @throws Exception
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $seller = $this->sellerRepo->findBySlug($slug);

            if (!$seller->is_verified) {
                throw new Exception('Seller is not verified', 404);
            }

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'status' => 'Success',
            'user' => SellerResource::make($seller)
        ]);
    }


    /**
     * Create Seller
     * METHOD: post
     * URL: /api/seller/create
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
            $data = (new CreateSellerRequestValidator)->attempt($request);
            $seller = $this->sellerService->create($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->errors()->first(),
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
            'seller' => SellerResource::make($seller)
        ]);
    }


    /**
     * Update Seller
     * METHOD: post
     * URL: /api/seller/update/{id}
     * @param Request $request
     * @param int $id
     * @throws ValidationException
     * @throws JWTException
     * @throws Exception
     * @throws Throwable
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = app(UpdateSellerRequestValidator::class)->attempt($request);
            $oldSeller = $this->sellerService->checkPermission($id);
            $seller = $this->sellerService->update($oldSeller, $data);
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
                'message' => '132'
            ], $e->getCode());
        }

        return response()->json([
            'status' => 'Success',
            'seller' => SellerResource::make($seller)
        ]);
    }


    /**
     * Delete Seller
     * METHOD: get
     * URL: /api/seller/delete/{id}
     * @param int $id
     * @throw JWTException
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $seller = $this->sellerService->checkPermission($id);
            $this->sellerService->delete($seller);

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
            'message' => 'Seller successfully deleted.'
        ]);
    }

}
