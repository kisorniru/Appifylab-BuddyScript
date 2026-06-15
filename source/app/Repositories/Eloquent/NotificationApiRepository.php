<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationApiRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NotificationApiRepository implements NotificationApiRepositoryInterface
{
    private const UNREAD_NOTIFICATION_COUNT_CACHE_SECONDS = 60;

    public function getUserNotifications(User $viewer, int $limit, ?int $cursor = null, bool $unreadOnly = false): Collection
    {
        return Notification::query()
            ->with('sender')
            ->where('receiver_id', $viewer->id)
            ->when($unreadOnly, fn ($query) => $query->unread())
            ->when($cursor, fn ($query) => $query->where('id', '<', $cursor))
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();
    }

    public function getUnreadNotificationCount(User $viewer): int
    {
        return Cache::remember(
            $this->unreadNotificationCountCacheKey($viewer),
            self::UNREAD_NOTIFICATION_COUNT_CACHE_SECONDS,
            fn () => Notification::query()
                ->where('receiver_id', $viewer->id)
                ->unread()
                ->count()
        );
    }

    public function markNotificationAsRead(User $viewer, int $notificationId): ?Notification
    {
        $notification = Notification::query()
            ->with('sender')
            ->where('receiver_id', $viewer->id)
            ->where('id', $notificationId)
            ->first();

        if ($notification && ! $notification->read_at) {
            $notification->forceFill(['read_at' => now()])->save();
            $this->forgetUnreadNotificationCount($viewer);
        }

        return $notification?->refresh()->load('sender');
    }

    public function createNotification(
        User $sender,
        User $receiver,
        string $type,
        string $notifiableType,
        int $notifiableId,
        array $data
    ): void {
        if ($sender->id === $receiver->id) {
            return;
        }

        Notification::updateOrCreate(
            [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'type' => $type,
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $notifiableId,
            ],
            [
                'data' => $data,
                'read_at' => null,
            ]
        );

        $this->forgetUnreadNotificationCount($receiver);
    }

    private function unreadNotificationCountCacheKey(User $viewer): string
    {
        return "user:{$viewer->id}:notifications:unread_count";
    }

    private function forgetUnreadNotificationCount(User $viewer): void
    {
        Cache::forget($this->unreadNotificationCountCacheKey($viewer));
    }
}
