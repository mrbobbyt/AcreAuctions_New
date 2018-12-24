<?php

namespace App\Services\Seller\Validators;

use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Exception;
use Validator;

class VerifySellerRequestValidator implements AbstractValidator
{
    protected $sellerRepo;

    public function __construct(SellerRepositoryContract $sellerRepo)
    {
        $this->sellerRepo = $sellerRepo;
    }


    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function attempt(Request $request)
    {
        if (!$seller = $this->sellerRepo->findByPk($request->input('id'))) {
            throw new Exception('Can not find seller.');
        }

        return [
            'seller' => $seller,
            'body' => $this->validateBody($request)
        ];
    }

    /**
     * Validate given data
     *
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        return $validator->validate();
    }
}