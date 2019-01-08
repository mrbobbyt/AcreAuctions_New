<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Exceptions\NoPermissionException;
use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\Validators\UpdateRequestUserServiceValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = $this->userRepo->authenticate();

        } catch (TokenExpiredException | TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Profile show error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'user' => UserResource::make($user)
        ]);
    }


    /**
     * Return user profile by id
     * METHOD: get
     * URL: /user/{id}
     * @param int $id
     * @return JsonResponse
     */
    public function view(int $id): JsonResponse
    {
        try {
            $user = $this->userRepo->findByPk($id);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User show error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'user' => UserResource::make($user)
        ]);
    }


    /**
     * Update auth user info
     * METHOD: post
     * URL: /{id}/update
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->userRepo->checkPermission($id);
            $data = (new UpdateRequestUserServiceValidator)->attempt($request);
            $user = $this->userService->update($data, $id);

        } catch (NoPermissionException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User update error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'user' => UserResource::make($user)
        ]);
    }


    /**
     * Delete auth user
     * METHOD: get
     * URL: /{id}/delete
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userRepo->checkPermission($id);
            $this->userService->delete($id);

        } catch (NoPermissionException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User delete error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'User successfully deleted.'
        ]);
    }
}
