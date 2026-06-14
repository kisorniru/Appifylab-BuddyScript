<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\FeedIndexRequest;
use App\Services\FeedApiService;

class FeedApiController extends Controller
{
    public function __construct(private FeedApiService $service)
    {
        parent::__construct();
    }

    public function getUserFeed(FeedIndexRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->getUserFeed($request),
            $this->getDefaultCacheTime(), 200
        );
    }
}
