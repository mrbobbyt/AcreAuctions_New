<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Services\Seller\Validators\ContinueAuthSellerRequestValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;

use App\Services\Seller\Contracts\SellerServiceContract;
use App\Repositories\Seller\SellerRepository;

use App\Services\Seller\Validators\CreateSellerRequestValidator;
use App\Services\Seller\Validators\UpdateSellerRequestValidator;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Seller\Exceptions\SellerAlreadyExistsException;
use App\Services\Seller\Exceptions\NoHaveRegisterToken;

class SellerController extends Controller
{
    protected $sellerRepo;
    protected $sellerService;

    public function __construct(
        SellerRepository $sellerRepo,
        SellerServiceContract $sellerService
    ) {
        $this->sellerRepo = $sellerRepo;
        $this->sellerService = $sellerService;
    }


    /**
     * View company-seller
     * METHOD: get
     * URL: /seller/{id}
     * @param string $slug
     * @return Response
     */
    public function view(string $slug): Response
    {
        try {
            $seller = $this->sellerRepo->findBySlug($slug);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Seller not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'Seller show error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['seller' => SellerResource::make($seller)]);
    }


    /**
     * Create Seller
     * METHOD: post
     * URL: /seller/create
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $dataSeller = (new CreateSellerRequestValidator)->attempt($request);
            $seller = $this->sellerService->create($dataSeller);
        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (SellerAlreadyExistsException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['seller' => SellerResource::make($seller)]);
    }


    /**
     * Update Seller
     * METHOD: put
     * URL: /seller/{id}/update
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        try {
            $data = (new UpdateSellerRequestValidator)->attempt($request);
            $seller = $this->sellerService->update($data, $id);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (SellerAlreadyExistsException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Seller not found'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['seller' => SellerResource::make($seller)]);
    }


    /**
     * Delete Seller
     * METHOD: delete
     * URL: /seller/{id}/delete
     * @param int $id
     * @return Response
     */
    public function delete(int $id)
    {
        try {
            $this->sellerService->delete($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Seller not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'Seller delete error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['message' => 'Seller successfully deleted.']);
    }

    /**
     * continue creating seller
     * METHOD: post
     * URL: /seller/continue-auth
     * @param Request $request
     * @return Response
     */
    public function continueAuth(Request $request): Response
    {
        try {
            $data = (new ContinueAuthSellerRequestValidator)->attempt($request);
            $this->sellerService->authSeller($data);
        } catch (NoHaveRegisterToken $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['message' => 'Seller successfully updated.']);
    }
}
