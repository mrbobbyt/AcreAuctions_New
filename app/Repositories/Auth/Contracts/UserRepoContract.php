<?php

namespace App\Repositories\Auth\Contracts;


interface UserRepoContract
{

    public function findByPk(int $id);

}