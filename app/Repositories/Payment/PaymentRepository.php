<?php
declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Models\Listing;
use App\Repositories\Payment\Contracts\PaymentRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Braintree_Gateway;

class PaymentRepository implements PaymentRepositoryContract
{
    /**
     * @param string $listingId
     * @return Model
     */
    public function findByListingId(string $listingId): Model
    {
        return Listing::query()->where('listing_id', $listingId)->first();
    }

    /**
     * @return mixed|string
     */
    public function getNewToken()
    {
        //@todo change before the start going to production
        $gateway = new Braintree_Gateway([
            'environment' => 'sandbox',
            'merchantId' => env('SANDBOX_TREE_MERCHANT_ID'),
            'publicKey' => env('SANDBOX_TREE_PUBLIC_KEY'),
            'privateKey' => env('SANDBOX_TREE_PRIVATE_KEY'),
        ]);

        return $gateway->clientToken()->generate();
    }

}