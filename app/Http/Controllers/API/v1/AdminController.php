<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Exports\ListingsExport;
use App\Exports\UsersExport;
use App\Http\Resources\ListingCollection;
use App\Http\Resources\UserCollection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Services\Admin\Validators\VerifySellerRequestValidator;
use App\Services\Admin\Validators\SearchUserRequestValidator;
use App\Services\Admin\Validators\ExportRequestValidator;
use App\Services\Admin\Validators\SearchListingRequestValidator;

use App\Repositories\Admin\Contracts\AdminRepositoryContract;
use App\Services\Admin\Contracts\AdminServiceContract;

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
     * @return JsonResponse
     */
    public function verifySeller(Request $request): JsonResponse
    {
        try {
            $data = app(VerifySellerRequestValidator::class)->attempt($request);
            $this->adminService->verifySeller($data['seller']);

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
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Verify seller error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => 'Seller successfully verified.'
        ]);
    }


    /**
     * METHOD: get
     * URL: /admin/all-users
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        try {
            $result = $this->adminRepo->getAllUsers();

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search user error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'users' => new UserCollection($result)
        ]);
    }


    /**
     * Search user by name and email
     * METHOD: get
     * URL: /admin/user-search
     * @param Request $request
     * @return JsonResponse
     */
    public function userSearch(Request $request): JsonResponse
    {
        try {
            $data = app(SearchUserRequestValidator::class)->attempt($request);
            $result = $this->adminRepo->findUsers($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search user error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'users' => new UserCollection($result)
        ]);
    }


    /**
     * Export users data
     * METHOD: post
     * URL: /admin/user-export
     * @param Request $request
     * @return JsonResponse
     */
    public function userExport(Request $request): JsonResponse
    {
        try {
            $data = app(ExportRequestValidator::class)->attempt($request);
            $file = (new UsersExport($data['body']['id']))
                ->download('users.'. $data['type']['type'], $data['format']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search user error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'file' => $file
        ]);
    }


    public function listingSearch(Request $request): JsonResponse
    {
        try {
            $data = app(SearchListingRequestValidator::class)->attempt($request);
            $result = $this->adminRepo->findListings($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search listing error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listings' => new ListingCollection($result)
        ]);
    }


    /**
     * METHOD: get
     * URL: /admin/all-listings
     * @return JsonResponse
     */
    public function getAllListings(): JsonResponse
    {
        try {
            $result = $this->adminRepo->getAllListings();

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search listing error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listings' => new ListingCollection($result)
        ]);
    }


    /**
     * Export listings data
     * METHOD: post
     * URL: /admin/listing-export
     * @param Request $request
     * @return JsonResponse
     */
    public function listingExport(Request $request): JsonResponse
    {
        try {
            $data = app(ExportRequestValidator::class)->attempt($request);
            $file = (new ListingsExport($data['body']['id']))
                ->download('listings.'. $data['type']['type'], $data['format']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Search listing error.'
            ], 500);
        }

        //return $file;
        return response()->json([
            'status' => 'Success',
            'file' => $file
        ]);
    }
}
