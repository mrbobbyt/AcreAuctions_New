<?php
declare(strict_types = 1);

namespace App\Repositories\Social\Contracts;


interface ShareRepositoryContract
{
    /**
     * @return array
     */
    public function getNetworks();

}
