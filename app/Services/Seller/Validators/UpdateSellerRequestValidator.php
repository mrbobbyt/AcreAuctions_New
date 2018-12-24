<?php

namespace App\Services\Seller\Validators;

use App\Repositories\Seller\SellerRepository;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Illuminate\Http\Request;
use Validator;

class UpdateSellerRequestValidator
{

    protected $userService;
    protected $sellerRepo;

    public function __construct(UserServiceContract $userService, SellerRepository $sellerRepo)
    {
        $this->userService = $userService;
        $this->sellerRepo = $sellerRepo;
    }


    /**
     * Return validated array of data
     *
     * @param Request $request
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function attempt(Request $request, int $id)
    {
        $userID = $this->userService->getID();
        $seller = $this->sellerRepo->findByPk($id);

        if ($seller->user_id != $userID) {
            throw new Exception('You are not permitted to update this seller.');
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
            'title' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
            'cover' => 'nullable|image',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255'
        ]);

        return $validator->validate();
    }
}