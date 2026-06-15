<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

interface NotificationApiRepositoryInterface
{
    public function getUserNotifications(User $viewer, int $limit, ?int $cursor = null, bool $unreadOnly = false): Collection;

    public function getUnreadNotificationCount(User $viewer): int;

    public function markNotificationAsRead(User $viewer, int $notificationId): ?Notification;

    public function createNotification(
        User $sender,
        User $receiver,
        string $type,
        string $notifiableType,
        int $notifiableId,
        array $data
    ): void;
}
