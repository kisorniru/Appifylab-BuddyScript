# Appifylab Assessment Documentation

## Overview

BuddyScript is a full-stack social feed application built from the provided Login, Register, and Feed HTML/CSS templates. The frontend is implemented in React, and the backend is implemented with Laravel, PostgreSQL, Redis, and Sanctum token authentication.

## What Was Built

- User registration and login.
- Protected feed page with frontend route guards.
- Public and private post creation.
- Text and media post support.
- Newest-first cursor-paginated feed.
- Like/love reactions with undo support.
- Comments and replies.
- Comment and reply image/voice attachments.
- Like/love reactions for comments and replies.
- Reactor lists for posts, comments, and replies.
- Notification creation and listing for comments, replies, and reactions.
- Swagger API documentation.

## Important Decisions

- **Laravel Sanctum** is used for API authentication because it fits the Laravel stack and supports bearer-token SPA usage cleanly.
- **Cursor pagination** is used for feed, comments, notifications, and reactor lists to avoid offset pagination issues on large datasets.
- **Repository and service layers** separate controller validation, business logic, and database work.
- **Response DTOs** keep API responses consistent with the `code`, `isSuccess`, `message`, and `responseBody` structure.
- **Visibility enforcement** is handled on the backend. Public posts are visible to all users, while private posts are only visible to the author.
- **Feed backfill** ensures users who register later can still see older public posts.
- **Media storage** currently uses local storage in Docker, with conversion support for web-friendly formats. The storage manager keeps this ready for a later S3 migration.

## Main API Flow

1. Frontend requests `GET /api/v1/user/auth/credential-key`.
2. Frontend encrypts password fields with the returned public key.
3. User registers or logs in and receives a Sanctum token.
4. Frontend stores the token and sends it as `Authorization: Bearer {apiToken}`.
5. Authenticated users can create posts, load feed data, comment, reply, react, and view notifications.

## Key Endpoints

```txt
GET  /api/v1/user/auth/credential-key
POST /api/v1/user/registration
POST /api/v1/user/login
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

## How To Run

```bash
chmod +x start_server.sh
./start_server.sh local --rebuild
```

Then open:

```txt
Frontend: http://localhost
API docs: http://localhost:8080/swagger/
```

## Current Scope Notes

The required assessment features are implemented. Extra features such as notifications, voice comments, video post support, and dark mode were also added. Deployment and video walkthrough links are planned as final submission deliverables.
