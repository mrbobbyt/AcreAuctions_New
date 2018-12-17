<?php

namespace App\Services\User\Validators;

use App\Repositories\User\Contracts\UserRepoContract;
use App\Rules\CheckRole;
use App\Services\User\Contracts\UserServiceContract;
use Illuminate\Http\Request;
use Exception;
use Validator;

class UpdateRequestUserServiceValidator
{

    protected $userService;

    public function __construct(UserServiceContract $userService)
    {
        $this->userService = $userService;
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
        if ($id != $userID) {
            throw new Exception('You are not permitted to update this user.');
        }

        return [
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
            'fname' => 'nullable|string|max:255|min:3',
            'lname' => 'nullable|string|max:255|min:3',
            'email' => 'nullable|string|email|max:255|unique:users',
            'role' => ['nullable','integer', new CheckRole]
        ]);

        return $validator->validate();
    }
}