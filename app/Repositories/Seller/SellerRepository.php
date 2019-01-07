<?php
declare(strict_types = 1);

namespace App\Repositories\Seller;

use App\Http\Resources\SellerResource;
use App\Models\Email;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class SellerRepository implements SellerRepositoryContract
{

    protected $model;
    protected $userService;

    public function __construct(Seller $seller, UserServiceContract $userService)
    {
        $this->model = $seller;
        $this->userService = $userService;
    }


    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     * @throws Exception
     */
    public function findBySlug(string $slug)
    {
        if ($seller = $this->model::query()->where('slug', $slug)->first()) {
            return $seller;
        }

        throw new Exception('Seller is not found', 404);
    }


    /**
     * Find seller by id
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id)
    {
        if ($seller = $this->model::query()->find($id)) {
            return $seller;
        }

        return false;
    }


    /**
     * Get related seller telephones
     * @param SellerResource $seller
     * @return array
     */
    public function getTelephones(SellerResource $seller): array
    {
        return $seller->telephones()
            ->where('entity_type', Telephone::TYPE_SELLER)
            ->get()->pluck('number')->toArray();
    }


    /**
     * Get related seller telephones
     * @param SellerResource $seller
     * @return array
     */
    public function getEmails(SellerResource $seller): array
    {
        return $seller->emails()
            ->where('entity_type', Email::TYPE_SELLER)
            ->get()->pluck('email')->toArray();
    }


    /**
     * Check if seller is not verified OR user is authenticate and not an admin or company head
     * @param Model $seller
     * @return bool
     * @throws Exception
     */
    public function checkVerification(Model $seller): bool
    {
        if ($seller->is_verified ||
                (JWTAuth::check(JWTAuth::getToken()) &&
                    ($this->userService->authenticate()->isAdmin() ||
                    $seller->user_id === $this->userService->getID() )
                )
        ) {
            return true;
        }

        throw new Exception('Seller is not verified', 404);
    }
}
