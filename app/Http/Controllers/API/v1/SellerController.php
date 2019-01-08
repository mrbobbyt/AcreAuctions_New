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
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\User\Exceptions\NoPermissionException;
use App\Services\Seller\Exceptions\SellerAlreadyExistsException;
use App\Repositories\Seller\Exceptions\SellerNotVerifiedException;

class SellerController extends Controller
{
    protected $sellerRepo;
    protected $sellerService;
    protected $userService;

    public function __construct(
        SellerRepository $sellerRepo,
        SellerServiceContract $sellerService,
        UserServiceContract $userService
    ) {
        $this->sellerRepo = $sellerRepo;
        $this->sellerService = $sellerService;
        $this->userService = $userService;
    }


    /**
     * View company-seller
     * METHOD: get
     * URL: /seller/{id}
     * @param string $slug
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $seller = $this->sellerRepo->findBySlug($slug);
            $this->sellerRepo->checkVerification($seller);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exist.'
            ], 404);
        } catch (SellerNotVerifiedException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller show error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => SellerResource::make($seller)
        ]);
    }


    /**
     * Create Seller
     * METHOD: post
     * URL: /seller/create
     * @param Request $request
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
        } catch (SellerAlreadyExistsException $e) {
            return response()->json([
                'status' => 'Error',
                'message' =>$e->getMessage()
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller create error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => SellerResource::make($seller)
        ]);
    }


    /**
     * Update Seller
     * METHOD: post
     * URL: /seller/{id}/update
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->sellerRepo->checkPermission($id);
            $data = (new UpdateSellerRequestValidator)->attempt($request);
            $seller = $this->sellerService->update($data, $id);

        } catch (NoPermissionException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (SellerAlreadyExistsException $e) {
            return response()->json([
                'status' => 'Error',
                'message' =>$e->getMessage()
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller update error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => SellerResource::make($seller)
        ]);
    }


    /**
     * Delete Seller
     * METHOD: get
     * URL: /seller/{id}/delete
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $this->sellerRepo->checkPermission($id);
            $this->sellerService->delete($id);

        } catch (NoPermissionException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller delete error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Seller successfully deleted.'
        ]);
    }

}
