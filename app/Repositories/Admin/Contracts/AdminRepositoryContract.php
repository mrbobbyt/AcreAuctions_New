<?php
declare(strict_types = 1);

namespace App\Repositories\Admin\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryContract
{
    /**
     * Find users by fname/lname/email
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findUsers(array $data);


    /**
     * Get all users
     * @return array
     */
    public function getAllUsers();


    /**
     * Find Listings by state/county/price/acres/status
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findListings(array $data);


    /**
     * Get all listings
     * @return LengthAwarePaginator
     */
    public function getAllListings();
}
