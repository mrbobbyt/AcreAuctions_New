<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;

use App\Services\Seller\Contracts\SellerServiceContract;
use App\Repositories\Seller\SellerRepository;
use App\Services\User\Contracts\UserServiceContract;

use App\Services\Seller\Validators\CreateSellerRequestValidator;
use App\Services\Seller\Validators\UpdateSellerRequestValidator;

use Illuminate\Validation\ValidationException;
use Exception;
use JWTAuth;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class SellerController extends Controller
{

    protected $sellerRepo;
    protected $sellerService;
    protected $userService;

    public function __construct(SellerRepository $sellerRepo, SellerServiceContract $sellerService, UserServiceContract $userService)
    {
        $this->sellerRepo = $sellerRepo;
        $this->sellerService = $sellerService;
        $this->userService = $userService;
    }


    /**
     * View company-seller
     * METHOD: get
     * URL: /api/seller/{id}
     * @param string $slug
     * @throws Exception
     * @throw JWTException
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $seller = $this->sellerRepo->findBySlug($slug);

            if (!$seller->is_verified) {
                // check if user is authenticate and not an admin or company head
                if ( !JWTAuth::check(JWTAuth::getToken()) ||
                    !$seller->getHead->isAdmin() ||
                    $seller->user_id !== $this->userService->getID()
                ) {
                    throw new Exception('Seller is not verified', 404);
                }
            }

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
            $seller = $this->sellerService->create($data);

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
            $data = (new UpdateSellerRequestValidator)->attempt($request);
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
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], $e->getCode());
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
