<?php
declare(strict_types = 1);

namespace App\Repositories\Admin\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryContract
{
    /**
     * Find users by fname/lname/email
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findUsers(array $data);

}
