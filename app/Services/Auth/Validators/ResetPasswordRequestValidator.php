<?php

namespace App\Services\Auth\Validators;

use App\Http\Resources\UserResource;
use App\Repositories\Auth\Contracts\UserRepoContract;
use App\Rules\CheckPasswordMatch;
use Illuminate\Http\Request;
use Validator;

class ResetPasswordRequestValidator implements AbstractValidator
{

    protected $userRepo;

    public function __construct(UserRepoContract $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request): array
    {
        $user = $this->userRepo->findByEmail($request->input('email'));

        return [
            'user' => UserResource::make($user),
            'body' => $this->validateBody($request)
        ];
    }

    /**
     * Validate given data
     *
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'current_password' => ['required', 'max:255', 'min:6', 'string', new CheckPasswordMatch],
            'password' => 'required|max:255|min:6|string|confirmed'
        ]);

        return $validator->validate();
    }
}