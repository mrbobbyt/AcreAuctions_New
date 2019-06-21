<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\ListingStatus;
use App\Repositories\Payment\Contracts\PaymentRepositoryContract;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Payment\Contracts\PaymentServiceContract;
use App\Models\Payments;
use Illuminate\Database\Eloquent\Model;
use Braintree_Gateway;

class PaymentService implements PaymentServiceContract
{
    protected $model;
    protected $userRepo;
    protected $paymentRepository;
    protected $imageService;

    public function __construct(
        Payments $payment,
        UserRepositoryContract $userRepo,
        PaymentRepositoryContract $paymentRepository
    )
    {
        $this->model = $payment;
        $this->userRepo = $userRepo;
        $this->paymentRepository = $paymentRepository;
    }


    /**
     * @param array $data
     * @return Model
     * @throws \Throwable
     */
    public function create(array $data): Model
    {
        $dataForSave = [];
        $payment = [];

        //@todo change before the start going to production
        $gateway = new Braintree_Gateway([
            'environment' => 'sandbox',
            'merchantId' => env('SANDBOX_TREE_MERCHANT_ID'),
            'publicKey' => env('SANDBOX_TREE_PUBLIC_KEY'),
            'privateKey' => env('SANDBOX_TREE_PRIVATE_KEY'),
        ]);

        $nonceFromTheClient = $data["payment_method"];
        $taxes = $data["taxes"];

        $listing = $this->paymentRepository->findByListingId($data['listing']);

        $result = $gateway->transaction()->sale([
            'amount' => $taxes,
            'paymentMethodNonce' => $nonceFromTheClient,
            'options' => [
                'submitForSettlement' => true,
            ],
        ]);

        if ($result->success) {
            $dataForSave['listing_id'] = $listing->id;
            $dataForSave['user_id'] = $data['user'];
            $dataForSave['price'] = $taxes;
            $dataForSave['total_price'] = $data['total'];
            $dataForSave['transaction_id'] = $result->transaction->id;
            $dataForSave['status'] = $result->transaction->status;
            $payment = $this->model->query()->make()->fill($dataForSave);
            $payment->saveOrFail();

            // Update status for listing, after payment
            $listing->status = ListingStatus::TYPE_SOLD;
            $listing->saveOrFail();
        }

        return $payment;
    }

    /**
     * Update user
     * @param string $data
     * @param int $id
     * @return Model
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(string $data, int $id): Model
    {
        $user = $this->userRepo->findByPk($id);
        $user->payment_token = $data;
        $user->saveOrFail();

        return $user;
    }
}
