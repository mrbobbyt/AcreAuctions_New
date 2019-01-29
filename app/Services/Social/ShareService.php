<?php
declare(strict_types = 1);

namespace App\Services\Social;

use App\Services\Social\Contracts\ShareServiceContract;
use Share;
use App\Models\Share as ShareModel;
use Throwable;

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


    /**
     * @param array $data
     * @return string
     * @throws Throwable
     */
    public function create(array $data): string
    {
        $share = ShareModel::query()->make()->fill([
            'entity_id' => $data['body']['listing_id'],
            'entity_type' => ShareModel::TYPE_LISTING,
            'network_id' => $data['body']['network_id'],
        ]);

        $share->saveOrFail();

        return 'Listing successfully shared to network.';
    }
}
