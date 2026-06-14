<?php

namespace App\Constants;

interface StorageUrl
{
    const USER_IMAGE = 'user/{userId}/image/{fileName}';

    const POST_MEDIA = 'post/{userId}/media/{fileName}';

    const POST_THUMBNAIL = 'post/{userId}/thumbnail/{fileName}';

    const COMMENT_MEDIA = 'comment/{userId}/media/{fileName}';

    const COMMENT_THUMBNAIL = 'comment/{userId}/thumbnail/{fileName}';
}
