<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Rules\CheckPassword;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use Illuminate\Http\Request;
use Validator;

class LoginRequestUserServiceValidator
{

    protected $userRepo;
    protected $userService;

    public function __construct(UserRepositoryContract $userRepo, UserAuthServiceContract $userService)
    {
        $this->userRepo = $userRepo;
        $this->userService = $userService;
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
            'body' => $this->validateBody($request, $user),
            'user' => $user
        ];
    }


    /**
     * Validate given data
     *
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function validateBody(Request $request, User $user): array
    {
        $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password'=> ['required', new CheckPassword($user)]
            ], [
                'email.exists' => 'The email or the password is wrong.',
            ]
        );

        return $validator->validate();
    }
}
