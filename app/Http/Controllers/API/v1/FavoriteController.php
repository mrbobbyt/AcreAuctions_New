<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Favorite\Contracts\FavoriteServiceContract;
use App\Services\Favorite\Validator\AddFavoriteValidateRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
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
     * @return Response
     */
    public function view(int $id): Response
    {
        try {
            $user = $this->userRepo->findByPk($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => ''], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['favorite_listing' => $user->getAllFavorites]);
    }


    /**
     * Add to favorite listings
     * METHOD: post
     * URL: /user/favorite/create
     * @param Request $request
     * @return Response
     */
    public function action(Request $request): Response
    {
        try {
            $data = (new AddFavoriteValidateRequest)->attempt($request);
            $result = $this->favorService->action($data);

        } catch (JWTException | Exception | Throwable $e) {
            return \response(['message' => 'Favorite listing action error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['result' => $result]);
    }

}
