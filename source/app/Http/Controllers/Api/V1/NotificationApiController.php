<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\NotificationIndexRequest;
use App\Services\NotificationApiService;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function __construct(private NotificationApiService $service)
    {
        parent::__construct();
    }

    public function getUserNotification(NotificationIndexRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->getUserNotification($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function postUserNotificationRead(Request $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->markUserNotificationAsRead($request),
            $this->getDefaultCacheTime(), 200
        );
    }
}
