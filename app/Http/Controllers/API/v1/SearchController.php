<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use App\Services\SearchListing\Validator\SearchListingRequestValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected $searchRepo;

    public function __construct(SearchListingRepositoryContract $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }


    /**
     * METHOD: get
     * URL: /search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $data = (new SearchListingRequestValidator())->attempt($request);

            if ($data['body']) {
                $result = $this->searchRepo->findByParams($data);
            } else {
                $result = $this->searchRepo->findAll();
            }

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listing' => $result
        ]);
    }
}
