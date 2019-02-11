<?php
declare(strict_types = 1);

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\Admin\Contracts\AdminRepositoryContract;

class AdminRepository implements AdminRepositoryContract
{
    public function findUsers(array $data)
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
                ->get();

        } else {
            $name = '%'.$data['body']['name'].'%';

            return User::query()
                ->where('fname', 'like', $name)
                ->orWhere('lname', 'like', $name)
                ->orWhere('email', 'like', $name)
                ->get();
        }
    }
}