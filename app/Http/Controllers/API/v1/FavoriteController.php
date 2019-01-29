<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Favorite\Contracts\FavoriteServiceContract;
use App\Services\Favorite\Validator\AddFavoriteValidateRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class FavoriteController extends Controller
{
    protected $userRepo;
    protected $favorService;

    public function __construct(UserRepositoryContract $userRepo, FavoriteServiceContract $favorService)
    {
        $this->userRepo = $userRepo;
        $this->favorService = $favorService;
    }


    /**
     * Get all user`s favorite listings
     * METHOD: get
     * URL: /user/{id}/favorite
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
                'message' => ''
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'favorite_listing' => $user->getAllFavorites,
        ]);
    }


    /**
     * Add to favorite listings
     * METHOD: post
     * URL: /user/favorite/create
     * @param Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        try {
            $data = (new AddFavoriteValidateRequest)->attempt($request);
            $result = $this->favorService->action($data);

        } catch (JWTException | Exception | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Favorite listing action error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'result' => $result
        ]);
    }

}
