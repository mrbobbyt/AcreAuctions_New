<?php
declare(strict_types = 1);

namespace App\Repositories\Admin;

use App\Models\Listing;
use App\Models\User;
use App\Repositories\Admin\Contracts\AdminRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminRepository implements AdminRepositoryContract
{
    /**
     * Find users by fname/lname/email
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findUsers(array $data): LengthAwarePaginator
    {
        if (strpos($data['body']['name'], ' ')) {
            list ($fname, $lname) = explode(' ', $data['body']['name']);

            return User::query()
                ->where([
                    ['fname', 'like', '%'.$fname.'%',],
                    ['lname', 'like', '%'.$lname.'%',]
                ])
                ->orWhere([
                    ['fname', 'like', '%'.$lname.'%',],
                    ['lname', 'like', '%'.$fname.'%',]
                ])
                ->orWhere('email', 'like', '%'.$fname.'%')
                ->orWhere('email', 'like', '%'.$lname.'%')
                ->paginate(15);

        } else {
            $name = '%'.$data['body']['name'].'%';

            return User::query()
                ->whereHas('telephones', function ($q) use ($name) {
                    $q->where('number',  'like', '%'.$name.'%');
                })
                ->orWhere('fname', 'like', $name)
                ->orWhere('lname', 'like', $name)
                ->orWhere('email', 'like', $name)
                ->orWhere('email', 'like', $name)
                ->paginate(15);
        }
    }


    /**
     * Get all users
     * @return LengthAwarePaginator
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        return User::paginate(15);
    }


    /**
     * Find Listings by state/county/price/acres/status
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findListings(array $data): LengthAwarePaginator
    {
        $listings = (new Listing)->newQuery();

        // Search by geo params
        $geoParams = array_only($data['body'], ['state', 'county']);
        if ($geoParams) {
            $listings->whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            });
        }

        // Search by range of acreage of listings
        if (isset($data['body']['acreage'])) {
            $acreageParam = $data['body']['acreage'];
            $listings->whereHas('geo', function ($q) use ($acreageParam) {
                $q->where('acreage', '>=', $acreageParam);
            });
        }

        // Search by range of price of listings
        if (isset($data['body']['price'])) {
            $priceParam = $data['body']['price'];
            $listings->whereHas('price', function ($q) use ($priceParam) {
                $q->where('price', '>=', $priceParam);
            });
        }

        // Search by status of listings
        if (isset($data['body']['status'])) {
            $listings->where('status', $data['body']['status']);
        }

        return $listings->paginate(15);
    }


    /**
     * Get all listings
     * @return Listing[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllListings()
    {
        return Listing::all();
    }

}
