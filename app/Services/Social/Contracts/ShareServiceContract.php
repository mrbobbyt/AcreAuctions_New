<?php
declare(strict_types = 1);

namespace App\Services\Social\Contracts;

use Throwable;

interface ShareServiceContract
{
    /**
     * Returns an array with links on fb, twitter, google +, reddit
     * @param string $url
     * @param string $title
     * @return array
     */
    public function shareSocials(string $url, string $title);


    /**
     * @param array $data
     * @return string
     * @throws Throwable
     */
    public function create(array $data);
}
