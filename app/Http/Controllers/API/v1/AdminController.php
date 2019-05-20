<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Exports\ListingsExport;
use App\Exports\UsersExport;
use App\Http\Resources\ListingCollection;
use App\Http\Resources\PostCollection;
use App\Http\Resources\SellerCollection;
use App\Http\Resources\UserCollection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\Admin\Validators\VerifySellerRequestValidator;
use App\Services\Admin\Validators\SearchUserRequestValidator;
use App\Services\Admin\Validators\ExportRequestValidator;
use App\Services\Admin\Validators\SearchListingRequestValidator;

use App\Repositories\Admin\Contracts\AdminRepositoryContract;
use App\Services\Admin\Contracts\AdminServiceContract;

use Illuminate\Http\Response;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    protected $adminService;
    protected $adminRepo;

    public function __construct(AdminServiceContract $adminService, AdminRepositoryContract $adminRepo)
    {
        $this->adminService = $adminService;
        $this->adminRepo = $adminRepo;
    }


    /**
     * Make seller verified
     * METHOD: put
     * URL: /admin/verify-seller
     * @param Request $request
     * @return Response
     */
    public function verifySeller(Request $request): Response
    {
        try {
            $data = app(VerifySellerRequestValidator::class)->attempt($request);
            $result = $this->adminService->verifySeller($data['seller']);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Seller not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(new SellerCollection($result));
    }


    /**
     * METHOD: get
     * URL: /admin/all-users
     * @return Response
     */
    public function getAllUsers(): Response
    {
        try {
            $result = $this->adminRepo->getAllUsers();
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(new UserCollection($result));
    }

    /**
     * METHOD: get
     * URL: /admin/all-posts
     * @return Response
     */
    public function getAllPosts(): Response
    {
        try {
            $result = $this->adminRepo->getAllPosts();
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(new PostCollection($result));
    }

    /**
     * METHOD: get
     * URL: /admin/all-sellers
     * @return Response
     */
    public function getAllSellers(): Response
    {
        try {
            $sellers = $this->adminService->getAllSellers();

            return \response(new SellerCollection($sellers));
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }
    }

    /**
     * Search user by name and email
     * METHOD: get
     * URL: /admin/user-search
     * @param Request $request
     * @return Response
     */
    public function userSearch(Request $request): Response
    {
        try {
            $data = app(SearchUserRequestValidator::class)->attempt($request);
            $result = $this->adminRepo->findUsers($data);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return \response(['message' => 'Search user error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['users' => new UserCollection($result)]);
    }


    /**
     * Export users data
     * METHOD: post
     * URL: /admin/user-export
     * @param Request $request
     * @return Response
     */
    public function userExport(Request $request): Response
    {
        try {
            $data = app(ExportRequestValidator::class)->attempt($request);
            $file = (new UsersExport($data['body']['id']))
                ->download('users.' . $data['type']['type'], $data['format']);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return \response(['message' => 'Search user error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['file' => $file]);
    }


    public function listingSearch(Request $request): Response
    {
        try {
            $data = app(SearchListingRequestValidator::class)->attempt($request);
            $result = $this->adminRepo->findListings($data);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return \response(['message' => 'Search listing error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['listings' => new ListingCollection($result)]);
    }


    /**
     * METHOD: get
     * URL: /admin/all-listings
     * @return Response
     */
    public function getAllListings(): Response
    {
        try {
            $result = $this->adminRepo->getAllListings();

        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(new ListingCollection($result));
    }


    /**
     * Export listings data
     * METHOD: post
     * URL: /admin/listing-export
     * @param Request $request
     * @return Response
     */
    public function listingExport(Request $request): Response
    {
        try {
            $data = app(ExportRequestValidator::class)->attempt($request);
            $file = (new ListingsExport($data['body']['id']))
                ->download('listings.' . $data['type']['type'], $data['format']);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return \response(['message' => 'Search listing error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['file' => $file]);
    }
}
