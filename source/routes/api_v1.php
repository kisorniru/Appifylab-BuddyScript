<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::get('/api/v1/user/feed', [
        'middleware' => ['auth:sanctum', 'throttle:50,1'],
        'uses' => 'FeedApiController@getUserFeed',
    ]);

    Route::post('/api/v1/user/post', [
        'middleware' => ['auth:sanctum', 'throttle:10,1'],
        'uses' => 'PostApiController@postUserPost',
    ]);

    Route::get('/api/v1/user/notification', [
        'middleware' => ['auth:sanctum', 'throttle:50,1'],
        'uses' => 'NotificationApiController@getUserNotification',
    ]);

    Route::post('/api/v1/user/notification/read', [
        'middleware' => ['auth:sanctum', 'throttle:30,1'],
        'uses' => 'NotificationApiController@postUserNotificationRead',
    ]);

    Route::post('/api/v1/user/post/reaction', [
        'middleware' => ['auth:sanctum', 'throttle:30,1'],
        'uses' => 'PostApiController@postUserPostReaction',
    ]);

    Route::get('/api/v1/user/post/reaction/user', [
        'middleware' => ['auth:sanctum', 'throttle:50,1'],
        'uses' => 'PostApiController@getUserPostReactionUser',
    ]);

    Route::get('/api/v1/user/post/comment', [
        'middleware' => ['auth:sanctum', 'throttle:50,1'],
        'uses' => 'PostApiController@getUserPostComment',
    ]);

    Route::post('/api/v1/user/post/comment', [
        'middleware' => ['auth:sanctum', 'throttle:20,1'],
        'uses' => 'PostApiController@postUserPostComment',
    ]);

    Route::post('/api/v1/user/post/comment/reaction', [
        'middleware' => ['auth:sanctum', 'throttle:30,1'],
        'uses' => 'PostApiController@postUserPostCommentReaction',
    ]);

    Route::get('/api/v1/user/post/comment/reaction/user', [
        'middleware' => ['auth:sanctum', 'throttle:50,1'],
        'uses' => 'PostApiController@getUserPostCommentReactionUser',
    ]);

    Route::post('/api/v1/user/login', [
        'middleware' => ['client.key'],
        'uses' => 'AuthApiController@userLoginPost',
    ]);

    Route::get('/api/v1/user/auth/credential-key', [
        'middleware' => ['throttle:20,1', 'client.key'],
        'uses' => 'AuthApiController@userAuthCredentialKeyGet',
    ]);

    Route::post('/api/v1/user/logout', [
        'middleware' => ['auth:sanctum'],
        'uses' => 'AuthApiController@userLogoutPost',
    ]);

    Route::post('/api/v1/user/logout-all', [
        'middleware' => ['auth:sanctum'],
        'uses' => 'AuthApiController@userLogoutAllPost',
    ]);

    Route::post('/api/v1/user/registration', [
        'middleware' => ['throttle:5,1', 'client.key'],
        'uses' => 'AuthApiController@userRegistrationPost',
    ]);

    Route::post('/api/v1/user/social-login', [
        'middleware' => ['throttle:15,1', 'client.key'],
        'uses' => 'AuthApiController@userSocialLoginPost',
    ]);
});
