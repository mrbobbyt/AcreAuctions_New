<?php
declare(strict_types = 1);

namespace App\Repositories\Social;

use App\Models\Network;
use App\Repositories\Social\Contracts\ShareRepositoryContract;

class ShareRepository implements ShareRepositoryContract
{
    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return Network::getAllFields();
    }
}