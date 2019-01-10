<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\SearchListing\SearchListingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    protected $searchRepo;

    public function __construct(SearchListingRepository $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }

    public function search(Request $request)
    {
        $geo = $request->only('state', 'acreage');
        $price = $request->only('price');

        if ($geo || $price) {
            $result = $this->searchRepo->findByParams($geo, $price);
        } else {
            $result = $this->searchRepo->findAll();
        }

        return response()->json([
            'status' => 'Success',
            'listing' => $result
        ]);
    }
}
