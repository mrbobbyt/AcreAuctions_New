<?php

namespace App\Http\Controllers\API\v1;

use App\Services\Seller\Contracts\SellerServiceContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Services\Seller\Validators\VerifySellerRequestValidator;

use Throwable;
use Exception;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{

    protected $sellerService;

    public function __construct(SellerServiceContract $sellerService)
    {
        $this->sellerService = $sellerService;
    }


    /**
     * Make seller verified
     *
     * METHOD: post
     * URL: /api/admin
     *
     * @param Request $request
     * @throws ValidationException
     * @throws Exception
     * @throws Throwable
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $data = app(VerifySellerRequestValidator::class)->attempt($request);
            $this->sellerService->verify($data['seller']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'seller' => 'Seller successfully verified.'
        ]);
    }

}
