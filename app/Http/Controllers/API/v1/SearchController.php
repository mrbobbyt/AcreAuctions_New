<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingCollection;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use App\Services\SearchListing\Validator\SearchListingRequestValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
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

            $result = $this->searchRepo->findAll($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'listings' => new ListingCollection($result)
        ]);
    }


    public function filters()
    {

    }
}
