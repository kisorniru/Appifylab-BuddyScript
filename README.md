# BuddyScript

BuddyScript is a Dockerized Laravel API and React social feed application built for the Appifylab full-stack assessment. It converts the provided login, registration, and feed HTML/CSS into a working React frontend backed by Laravel, PostgreSQL, Redis, and Sanctum authentication.

## Implemented Assessment Features

- React login, registration, and feed pages based on the supplied template design.
- Secure Laravel Sanctum authentication with guarded frontend routes.
- Registration with first name, optional last name, email, password, and password confirmation.
- Encrypted password transport for login and registration using a temporary credential key API.
- Protected feed route available only to authenticated users.
- Public/private post creation with text and image/video media upload.
- Public posts visible to all users, including users who register after posts already exist.
- Private posts visible only to the author.
- Cursor-paginated feed ordered newest first.
- Post like/love reactions with undo behavior and correct viewer reaction state.
- Full post reactors popup showing avatar, name, reaction type, and reaction time.
- Comments and replies with image/voice attachments.
- Comment/reply like/love reactions with undo behavior.
- Full comment/reply reactors popup.
- Notifications for post comments, replies, post reactions, and comment reactions.
- Swagger API documentation at `source/public/swagger/swagger.yaml`.
- API documentation is password protected, where username : `developer` and password : `password`

## Tech Stack

- Frontend: React, Vite, React Router, Bootstrap/template assets
- Backend: Laravel, Sanctum, PostgreSQL, Redis
- Runtime: Docker Compose, Nginx, PHP-FPM
- API docs: Swagger/OpenAPI 2.0

## Local URLs

```txt
React frontend:      http://localhost
Laravel API:         http://localhost:8080/api/v1
API Documentation:   http://localhost:8080/swagger/
PostgreSQL host:     localhost:54532
Redis host:          localhost:6379
```

## Run With Docker

```bash
chmod +x start_server.sh
./start_server.sh local --rebuild
```

Use a fresh database only when data loss is intended:

```bash
./start_server.sh local --rebuild --fresh
```

Useful checks:

```bash
docker compose ps
docker compose exec php php artisan route:list --path=api/v1/user
docker compose build frontend
docker compose up -d frontend
```

## Important API Routes

Public auth routes require `X-Client-Key`.

```txt
GET  /api/v1/user/auth/credential-key
POST /api/v1/user/registration
POST /api/v1/user/login
```

Authenticated routes require `Authorization: Bearer {apiToken}`.

```txt
GET  /api/v1/user/feed
POST /api/v1/user/post
POST /api/v1/user/post/reaction
GET  /api/v1/user/post/reaction/user
GET  /api/v1/user/post/comment
POST /api/v1/user/post/comment
POST /api/v1/user/post/comment/reaction
GET  /api/v1/user/post/comment/reaction/user
GET  /api/v1/user/notification
POST /api/v1/user/notification/read
POST /api/v1/user/logout
```

API responses follow the project pattern:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Success message",
  "responseBody": {}
}
```

## Project Structure

```txt
frontend/                         React public SPA
source/                           Laravel API and app
source/routes/api_v1.php          API route definitions
source/app/Services               Business logic and transactions
source/app/Repositories           Repository contracts and Eloquent implementations
source/app/Http/Requests          Validation request classes
source/app/Http/Responses         Response DTO classes
source/database/migrations        Database schema
source/public/swagger/swagger.yaml Swagger API contract
```

## Notes For Review


- Feed and notification pagination use cursor-based loading for better scale characteristics.
- Posts store visibility at the database level and the backend enforces visibility when loading feed, comments, reactions, and reactor lists.
- Media is stored locally in Docker now, with conversion support for WebP/WebM where applicable. The storage layer is isolated for later S3 migration.
