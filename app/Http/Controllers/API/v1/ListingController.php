<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

class ListingController extends Controller
{

    protected $listingService;
    protected $listingRepo;

    public function __construct(ListingServiceContract $listingService, ListingRepositoryContract $listingRepo)
    {
        $this->listingService = $listingService;
        $this->listingRepo = $listingRepo;
    }


    public function view(string $slug)
    {
        try {
            $listing = $this->listingRepo->findBySlug($slug);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'status' => 'Success',
            'user' => ListingResource::make($listing)
        ]);
    }

}
