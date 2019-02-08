<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\ListingResource;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use App\Services\SearchListing\Validator\SearchListingRequestValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
            'listing' => $this->paginate(ListingResource::collection($result), url()->current())
        ]);
    }


    /**
     * Make search results paginated
     * @param $items
     * @param null $baseUrl
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate($items, $baseUrl = null, $perPage = 5, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        $lap = new LengthAwarePaginator($items->forPage($page, $perPage),
            $items->count(),
            $perPage, $page, $options);

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
}
