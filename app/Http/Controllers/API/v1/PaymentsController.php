<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\UserResource;
use App\Repositories\Payment\PaymentRepository;
use App\Services\Payment\Validators\PaymentValidateRequest;
use App\Services\Payment\PaymentService;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class PaymentsController extends Controller
{
    protected $paymentsService;
    protected $paymentsRepository;

    public function __construct(
        PaymentService $paymentsService,
        PaymentRepository $paymentsRepository
    )
    {
        $this->paymentsService = $paymentsService;
        $this->paymentsRepository = $paymentsRepository;
    }


    /**
     * Create Payment
     * METHOD: post
     * URL: /payment/create
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {

        try {
            $data = (new PaymentValidateRequest)->attempt($request);
            $payment = $this->paymentsService->create($data);

        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['payment' => PaymentResource::make($payment)]);
    }

    /**
     * Update token for BrainTree
     * METHOD: post
     * URL: /payment/{id}
     * @param int $id
     * @return Response
     */
    public function generateNewPaymentToken(int $id): Response
    {
        try {
            $data = $this->paymentsRepository->getNewToken();
            $user = $this->paymentsService->update($data, $id);
        }  catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return \response(['user' => UserResource::make($user)]);
    }
}
