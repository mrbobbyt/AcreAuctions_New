<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\User\Contracts\UserRepositoryContract;
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
use Exception;

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
     * @throws JWTException
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     * @throws Throwable
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = $this->userRepo->authenticate();

        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 400);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 400);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
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
                'message' => $e->getMessage()
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
     * @throws ValidationException
     * @throws JWTException
     * @throws Exception
     * @throws Throwable
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->userRepo->checkPermission($id);
            $data = (new UpdateRequestUserServiceValidator)->attempt($request);
            $user = $this->userService->update($data, $id);

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
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
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
     * @throws JWTException
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userRepo->checkPermission($id);
            $this->userService->delete($id);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 403);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'User successfully deleted.'
        ]);
    }
}
