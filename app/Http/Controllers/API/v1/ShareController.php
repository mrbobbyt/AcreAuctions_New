<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Repositories\Social\Contracts\ShareRepositoryContract;
use App\Services\Social\Contracts\ShareServiceContract;
use App\Services\Social\Validators\ShareRequestValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @return Response
     */
    public function getNetworks(): Response
    {
        return \response(['networks' => $this->shareRepo->getNetworks()]);
    }


    /**
     * Return list of networks
     * METHOD: post
     * URL: /share/create
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = (new ShareRequestValidator)->attempt($request);
            $result = $this->shareService->create($data);

        } catch (Throwable $e) {
            return \response(['message' => 'Share save error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['result' => $result]);
    }
}
