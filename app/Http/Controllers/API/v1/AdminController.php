<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Services\Seller\Contracts\SellerServiceContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Services\Seller\Validators\VerifySellerRequestValidator;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    protected $sellerService;

    public function __construct(SellerServiceContract $sellerService)
    {
        $this->sellerService = $sellerService;
    }


    /**
     * Make seller verified
     * METHOD: post
     * URL: /admin/verify-seller
     * @param Request $request
     * @return JsonResponse
     */
    public function verifySeller(Request $request): JsonResponse
    {
        try {
            $data = app(VerifySellerRequestValidator::class)->attempt($request);
            $this->sellerService->verify($data['seller']);

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

}
