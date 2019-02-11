<?php
declare(strict_types = 1);

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\Admin\Contracts\AdminRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminRepository implements AdminRepositoryContract
{
    /**
     * Find users by fname/lname/email
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findUsers(array $data): LengthAwarePaginator
    {
        if (strpos($data['body']['name'], ' ')) {
            list ($fname, $lname) = explode(' ', $data['body']['name']);

            return User::query()
                ->where([
                    ['fname', 'like', '%'.$fname.'%',],
                    ['lname', 'like', '%'.$lname.'%',]
                ])
                ->orWhere([
                    ['fname', 'like', '%'.$lname.'%',],
                    ['lname', 'like', '%'.$fname.'%',]
                ])
                ->orWhere('email', 'like', '%'.$fname.'%')
                ->orWhere('email', 'like', '%'.$lname.'%')
                ->paginate(20);

        } else {
            $name = '%'.$data['body']['name'].'%';

            return User::query()
                ->where('fname', 'like', $name)
                ->orWhere('lname', 'like', $name)
                ->orWhere('email', 'like', $name)
                ->paginate(20);
        }
    }


    /**
     * Get all users
     * @return LengthAwarePaginator
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        return User::paginate(20);
    }

}
