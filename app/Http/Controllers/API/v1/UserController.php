<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\Validators\UpdateRequestUserServiceValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    protected $userService;
    protected $userRepo;

    public function __construct(UserServiceContract $userService, UserRepositoryContract $userRepo)
    {
        $this->userService = $userService;
        $this->userRepo = $userRepo;
    }


    /**
     * Return auth user profile
     * METHOD: get
     * URL: /user/profile
     * @return Response
     */
    public function profile(): Response
    {
        try {
            $user = $this->userRepo->authenticate();
        } catch (TokenExpiredException | TokenInvalidException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (JWTException | Throwable $e) {
            return \response(['message' => 'Profile show error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['user' => UserResource::make($user)]);
    }


    /**
     * Return user profile by id
     * METHOD: get
     * URL: /user/{id}
     * @param int $id
     * @return Response
     */
    public function view(int $id): Response
    {
        try {
            $user = $this->userRepo->findByPk($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'User show error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['user' => UserResource::make($user)]);
    }


    /**
     * Update auth user info
     * METHOD: put
     * URL: /{id}/update
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        try {
            $data = (new UpdateRequestUserServiceValidator)->attempt($request);
            $user = $this->userService->update($data, $id);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);
        } catch (JWTException | Throwable $e) {
            return \response(['message' => 'User update error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['user' => UserResource::make($user)]);
    }


    /**
     * Delete auth user
     * METHOD: delete
     * URL: /{id}/delete
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        try {
            $this->userService->delete($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'User delete error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['message' => 'User successfully deleted.']);
    }
}
