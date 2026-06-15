# BuddyScript Frontend Authentication API Contract

Base URL:

```bash
VITE_API_BASE_URL=http://localhost:8080/api/v1
VITE_CLIENT_SECRET_KEY=base64:S0FZaUFwaURPTC1raXNvcm5pcnVAZ21haWw=
```

Common headers before login:

```http
Accept: application/json
Content-Type: application/json
X-Client-Key: base64:S0FZaUFwaURPTC1raXNvcm5pcnVAZ21haWw=
```

Common headers after login:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <responseBody.apiToken>
```

## 1. Registration

`POST /api/v1/user/registration`

Request:

```json
{
  "firstName": "Noor-A-Alam",
  "lastName": "Siddique",
  "email": "siddique@example.com",
  "password": "password123",
  "passwordConfirm": "password123"
}
```

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Registration successful",
  "responseBody": {
    "id": 1,
    "name": "Noor-A-Alam Siddique",
    "email": "siddique@example.com",
    "firstTimeLogin": true,
    "apiToken": "1|plain-text-sanctum-token"
  }
}
```

## 2. Login

`POST /api/v1/user/login`

Request:

```json
{
  "email": "siddique@example.com",
  "password": "password123"
}
```

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Login successful",
  "responseBody": {
    "id": 1,
    "name": "Noor-A-Alam Siddique",
    "email": "siddique@example.com",
    "firstTimeLogin": false,
    "apiToken": "1|plain-text-sanctum-token"
  }
}
```

## 3. Logout current device

`POST /api/v1/user/logout`

Header:

```http
Authorization: Bearer <apiToken>
```

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Successfully logged out"
}
```

## 4. Social Login

`POST /api/v1/user/social-login`

Request:

```json
{
  "firstName": "Noor-A-Alam",
  "lastName": "Siddique",
  "email": "siddique@example.com",
  "phone": "+8801700000000",
  "provider": "google",
  "providerId": "google-provider-user-id"
}
```

Success response shape is the same as login:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Login successful",
  "responseBody": {
    "id": 1,
    "name": "Noor-A-Alam Siddique",
    "email": "siddique@example.com",
    "firstTimeLogin": true,
    "apiToken": "1|plain-text-sanctum-token"
  }
}
```

## 5. Logout all devices

`POST /api/v1/user/logout-all`

Header:

```http
Authorization: Bearer <apiToken>
```

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Successfully logged out from all devices"
}
```

## 6. Feed Timeline

`GET /api/v1/user/feed?limit=10&cursor=123`

Header:

```http
Authorization: Bearer <apiToken>
```

Query:

```json
{
  "limit": "optional integer, default 10, max 20",
  "cursor": "optional feed item id for loading older posts"
}
```

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Feed fetched successfully",
  "responseBody": {
    "items": [
      {
        "id": 1,
        "postId": 10,
        "author": {
          "id": 2,
          "name": "Dylan Field",
          "avatar": "profile.png"
        },
        "text": "Post preview/body text",
        "image": "https://picsum.photos/seed/demo/600/400",
        "postType": 2,
        "visibility": 1,
        "reactionCount": 12,
        "commentCount": 4,
        "shareCount": 0,
        "viewerReaction": 1,
        "createdAt": "2026-06-15T10:00:00.000000Z"
      }
    ],
    "meta": {
      "next_cursor": 1,
      "has_more": true
    }
  }
}
```

## 7. Create User Post

`POST /api/v1/user/post`

Header:

```http
Authorization: Bearer <apiToken>
```

Request:

Use `multipart/form-data` when uploading media:

```http
body=Post text content
visibility=1
media=@photo.jpg
thumbnail=@thumb.jpg
```

Fallback JSON request, only when the media is already uploaded:

```json
{
  "title": "Optional post title",
  "body": "Post text content",
  "visibility": 1,
  "mediaUrl": "https://example.com/image.jpg",
  "thumbnailUrl": "https://example.com/thumb.jpg",
  "mediaType": 1
}
```

Notes:

- `body` is required.
- `visibility`: `1` public, `2` private. Defaults to public.
- `media` is an optional upload file. Supported image formats become WebP; supported video formats become WebM.
- `thumbnail` is optional and is converted to WebP.
- `mediaUrl`, `thumbnailUrl`, and `mediaType` are stored URLs/metadata after upload. They are accepted only as a transitional fallback when a file is already uploaded.
- `mediaType`: `1` image, `2` video. The API detects this automatically when `media` is uploaded.

Success response:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Post created successfully",
  "responseBody": {
    "id": 3000,
    "postId": 120,
    "author": {
      "id": 4,
      "name": "Temp User 3",
      "avatar": "https://picsum.photos/200/300"
    },
    "text": "Post text content",
    "image": "https://example.com/image.jpg",
    "postType": 2,
    "visibility": 1,
    "reactionCount": 0,
    "commentCount": 0,
    "shareCount": 0,
    "viewerReaction": null,
    "createdAt": "2026-06-15T10:00:00.000000Z"
  }
}
```
