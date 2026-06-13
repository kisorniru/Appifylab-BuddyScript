# BuddyScript React Frontend

This folder contains the public React/Vite frontend for the Appifylab assessment. It converts the provided `login.html`, `registration.html`, and `feed.html` designs into React pages and integrates with the Laravel API in `../source`.

## Frontend Scope

- Mobile-first login and registration pages based on the supplied template.
- Route guards for logged-in and logged-out users.
- Credential-key based login and registration integration.
- API-backed feed timeline with cursor pagination.
- Post composer with text, media preview, upload, and public/private visibility icons.
- Post media rendering for images and videos.
- Like/love reaction picker with undo behavior.
- Comment and reply threads with image and voice attachments.
- Comment and reply reaction picker with reactor popup.
- Notification dropdown with all/unread filtering, infinite scroll, unread counter, and read state.
- Static template sections for stories, sidebars, suggested people, and friends.

## Tech Stack

- React
- Vite
- React Router
- Template CSS and assets from `public/assets`
- Fetch-based API client

## Local Setup

The Dockerized frontend is served at:

```txt
http://localhost
```

The API base URL is:

```txt
http://localhost:8080/api/v1
```

For local Vite development:

```bash
npm install
npm run dev
```

For production build:

```bash
npm run build
```

In Docker:

```bash
docker compose build frontend
docker compose up -d frontend
```

## Implemented Screens

- `/login` authenticates through `/user/login`.
- `/register` registers through `/user/registration`.
- `/feed` is protected and loads the API-backed social feed.
- Logged-in users are redirected away from `/login` and `/register`.
- Logged-out users are redirected from `/feed` to `/login`.

## Feed Features

- Create public or private posts.
- Attach image/video media to posts.
- Newest-first cursor-paginated feed.
- Like/love/undo reactions on posts.
- Full post reactor popup.
- Comments and replies.
- Image and voice comment attachments.
- Like/love/undo reactions on comments and replies.
- Full comment/reply reactor popup.
- Notification dropdown with all/unread filtering and read state.
- Logout through the Laravel API.

## API Client Notes

The API client lives in `src/api`.

- `authApi.js` handles credential-key encryption, login, registration, and logout.
- `feedApi.js` handles feed, post creation, reactions, comments, reactor lists, and notifications.
- Tokens and user data are stored through `apiClient.js`.

Laravel responses are normalized from the project shape:

```json
{
  "code": 200,
  "isSuccess": true,
  "message": "Success message",
  "responseBody": {}
}
```

## Assets

Template assets are served from:

```txt
frontend/public/assets/
```

Use `AssetImage` for template images so production paths stay stable.

## Feed Component Structure

```txt
src/pages/Feed.jsx                         Page state and API orchestration
src/components/feed/FeedPageSections.jsx   Feed section exports
src/components/feed/FeedHeader.jsx         Header, profile menu, mobile navigation
src/components/feed/FeedStaticSections.jsx Stories and sidebar template sections
src/components/feed/FeedComposer.jsx       Post composer and toast alert
src/components/feed/FeedTimeline.jsx       Timeline loading, empty, and pagination states
src/components/feed/FeedPost.jsx           Post card rendering
src/components/feed/FeedCommentBox.jsx     Comment input and attachment previews
src/components/feed/FeedCommentThread.jsx  Comment and reply display
src/components/feed/FeedReactions.jsx      Reaction picker and reactor modal
src/components/feed/FeedIcons.jsx          Shared feed SVG icons
src/components/feed/feedUtils.js           Feed formatting and normalization helpers
src/data/feedStaticData.js                 Static template data
```
