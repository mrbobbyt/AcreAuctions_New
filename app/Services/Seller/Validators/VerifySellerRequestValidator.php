<?php
declare(strict_types = 1);

namespace App\Services\Seller\Validators;

use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
     * @param Request $request
     * @return array
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function attempt(Request $request)
    {
        $seller = $this->sellerRepo->findByPk((int)$request->input('id'));

        return [
            'seller' => $seller,
            'body' => $this->validateBody($request)
        ];
    }

    /**
     * Validate given data
     * @param Request $request
     * @throws ValidationException
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