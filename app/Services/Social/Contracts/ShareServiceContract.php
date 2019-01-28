<?php
declare(strict_types = 1);

namespace App\Services\Social\Contracts;

interface ShareServiceContract
{
    /**
     * Returns an array with links on fb, twitter, google +, reddit
     * @param string $url
     * @param string $title
     * @return array
     */
    public function shareSocials(string $url, string $title);
}
