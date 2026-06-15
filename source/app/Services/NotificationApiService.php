<?php

namespace App\Services;

use App\Exceptions\ParameterException;
use App\Http\Requests\NotificationIndexRequest;
use App\Http\Responses\FeedAuthorResponse;
use App\Http\Responses\FeedMetaResponse;
use App\Http\Responses\NotificationItemResponse;
use App\Http\Responses\NotificationResponse;
use App\Http\Responses\NotificationResponseBody;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationApiRepositoryInterface;
use Illuminate\Http\Request;

class NotificationApiService
{
    public function __construct(private NotificationApiRepositoryInterface $notificationApiRepository) {}

    public function getUserNotification(NotificationIndexRequest $request): NotificationResponse
    {
        $viewer = $request->user();
        $limit = (int) ($request->input('limit') ?: 10);
        $cursor = $request->filled('cursor') ? (int) $request->input('cursor') : null;

        $notifications = $this->notificationApiRepository->getUserNotifications(
            $viewer,
            $limit,
            $cursor,
            $request->boolean('unreadOnly')
        );
        $hasMore = $notifications->count() > $limit;
        $visibleNotifications = $notifications->take($limit)->values();

        $body = new NotificationResponseBody;
        $body->items = $visibleNotifications
            ->map(fn (Notification $notification) => $this->toNotificationItemResponse($notification))
            ->all();
        $body->meta = $this->notificationMeta($viewer, $hasMore, $hasMore ? $visibleNotifications->last()?->id : null);

        $response = new NotificationResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Notifications fetched successfully';
        $response->responseBody = $body;

        return $response;
    }

    public function markUserNotificationAsRead(Request $request): NotificationResponse
    {
        $viewer = $request->user();
        $notificationId = (int) $request->input('notificationId');

        if ($notificationId < 1) {
            throw new ParameterException;
        }

        $notification = $this->notificationApiRepository->markNotificationAsRead($viewer, $notificationId);
        if (! $notification) {
            throw new ParameterException;
        }

        $body = new NotificationResponseBody;
        $body->items = [$this->toNotificationItemResponse($notification)];
        $body->meta = $this->notificationMeta($viewer, false, null);

        $response = new NotificationResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Notification marked as read';
        $response->responseBody = $body;

        return $response;
    }

    private function notificationMeta($viewer, bool $hasMore, ?int $nextCursor): FeedMetaResponse
    {
        $meta = new FeedMetaResponse;
        $meta->next_cursor = $nextCursor;
        $meta->has_more = $hasMore;
        $meta->unread_count = $this->notificationApiRepository->getUnreadNotificationCount($viewer);

        return $meta;
    }

    private function toNotificationItemResponse(Notification $notification): NotificationItemResponse
    {
        $data = $notification->data ?: [];

        $item = new NotificationItemResponse;
        $item->id = $notification->id;
        $item->type = $notification->type;
        $item->sender = $notification->sender
            ? $this->toAuthorResponse($notification->sender->id, $notification->sender->name, $notification->sender->profile_picture)
            : null;
        $item->message = $data['message'] ?? '';
        $item->notifiableType = $notification->notifiable_type;
        $item->notifiableId = $notification->notifiable_id;
        $item->data = $data;
        $item->isRead = $notification->read_at !== null;
        $item->readAt = $notification->read_at?->toISOString();
        $item->createdAt = $notification->created_at?->toISOString();

        return $item;
    }

    private function toAuthorResponse(?int $id, ?string $name, ?string $avatar): FeedAuthorResponse
    {
        $author = new FeedAuthorResponse;
        $author->id = $id;
        $author->name = $name;
        $author->avatar = $avatar;

        return $author;
    }
}
