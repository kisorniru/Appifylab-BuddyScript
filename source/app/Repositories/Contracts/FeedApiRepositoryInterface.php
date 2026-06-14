<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

interface FeedApiRepositoryInterface
{
    public function ensureFeedItemsForViewer(User $viewer): void;

    public function getUserFeed(User $viewer, int $limit, ?int $cursor = null): Collection;

    public function getPostReactors(Post $post, int $limit = 4): Collection;
}
