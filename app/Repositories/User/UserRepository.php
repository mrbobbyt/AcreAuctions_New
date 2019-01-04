<?php
declare(strict_types = 1);

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepositoryContract
{

    /**
     * Find user using id
     *
     * @param int $id
     * @throws Exception
     * @return Model
     */
    public function findByPk(int $id): Model
    {
        if ($user = User::query()->find($id)) {
            return $user;
        }

        throw new Exception('User not exist.');
    }


    /**
     * @param string $email
     * @return User|\Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function findByEmail(string $email)
    {
        return User::query()->where('email', $email)->first();
    }


    /**
     * Check if user exists in db
     *
     * @param string $email
     * @return bool
     */
    public function checkUserExists(string $email): bool
    {
        return User::query()->where('email', $email)->exists();
    }
}
