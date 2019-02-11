<?php
declare(strict_types = 1);

namespace App\Repositories\Admin\Contracts;

interface AdminRepositoryContract
{
    public function findUsers(array $data);
}