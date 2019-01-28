<?php
declare(strict_types = 1);

namespace App\Services\Social;

use App\Services\Social\Contracts\ShareServiceContract;
use Share;

class ShareService implements ShareServiceContract
{
    /**
     * Returns an array with links on fb, twitter, google +, reddit
     * @param string $url
     * @param string $title
     * @return array
     */
    public function shareSocials(string $url, string $title): array
    {
        return Share::load($url, $title)->services('facebook', 'twitter', 'gplus', 'reddit');
    }
}
