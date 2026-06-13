# BuddyScript Backend

This directory contains the Laravel API for BuddyScript. It owns authentication, feed data, post creation, comments, replies, reactions, notifications, media storage, validation, and Swagger API documentation.

## Backend Scope

- Sanctum token authentication for protected APIs.
- Public registration and login using the project credential-key password flow.
- Cursor-paginated feed API ordered newest first.
- Public/private post visibility enforced by backend queries.
- Post media upload with local storage now and an isolated storage layer for later S3 migration.
- Comment and reply APIs with optional image or voice attachments.
- Like/love reactions for posts, comments, and replies with undo behavior.
- Reactor list APIs for posts and comments/replies.
- Notifications for post comments, post reactions, comment replies, and comment reactions.
- Validation messages under `lang/en/validation`.
- Swagger contract at `public/swagger/swagger.yaml`.
- Swagger is protected with username : `developer` and password : `password`

## Tech Stack

- PHP and Laravel
- Laravel Sanctum
- PostgreSQL
- Redis
- Nginx and PHP-FPM through Docker Compose
- Swagger/OpenAPI 2.0
- Local filesystem media storage

## Important Paths

```txt
routes/api_v1.php                         API route definitions
app/Http/Controllers/Api/V1               API controllers
app/Services                              Business logic and transactions
app/Repositories                          Repository interfaces and Eloquent implementations
app/Http/Requests                         Form request validation
app/Http/Responses                        Response DTO classes
app/Models                                Eloquent models
app/Managers/StorageManager.php           Media storage handling
database/migrations                       Database schema
database/seeders                          Demo and bootstrap data
public/swagger/swagger.yaml               API documentation
```

## API Style

The backend follows the project pattern:

```txt
Route -> Api/V1 Controller -> Service -> Repository -> Response object
```

Successful responses use:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Success message",
  "responseBody": {}
}
```

Public auth routes require `X-Client-Key`. Authenticated social routes require:

```http
Authorization: Bearer {apiToken}
```

## Useful Docker Commands

Run migrations:

```bash
docker compose exec php php artisan migrate
```

Seed demo data:

```bash
docker compose exec php php artisan db:seed
```

Inspect API routes:

```bash
docker compose exec php php artisan route:list --path=api/v1
```

Clear Laravel caches:

```bash
docker compose exec php php artisan optimize:clear
```

Run tests when available:

```bash
docker compose exec php php artisan test
```
