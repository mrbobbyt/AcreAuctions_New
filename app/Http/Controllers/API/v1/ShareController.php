<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\Social\Contracts\ShareRepositoryContract;
use App\Services\Social\Contracts\ShareServiceContract;
use App\Services\Social\Validators\ShareRequestValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

class ShareController extends Controller
{
    protected $shareRepo;
    protected $shareService;

    public function __construct(ShareRepositoryContract $shareRepo, ShareServiceContract $shareService)
    {
        $this->shareRepo = $shareRepo;
        $this->shareService = $shareService;
    }


    /**
     * Return list of networks
     * METHOD: get
     * URL: /share/list
     * @return JsonResponse
     */
    public function getNetworks(): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'networks' => $this->shareRepo->getNetworks()
        ]);
    }


    /**
     * Return list of networks
     * METHOD: post
     * URL: /share/create
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $data = (new ShareRequestValidator)->attempt($request);
            $result = $this->shareService->create($data);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Share save error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'result' => $result
        ]);
    }
}
