<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\CommentController;
use App\Http\Controllers\Api\v1\ReactionController;
use App\Http\Controllers\Api\v1\TagController;
use App\Http\Controllers\Api\v1\UserController;


/**
 *
 * API v1
 */
Route::prefix('v1')->group(function () {
    /**
     *
     * Database
     */
    Route::get('/test-db-connection', function () {
        try {
            DB::connection()->getPdo();
            return 'Database connection is successful';
        } catch (\Exception $e) {
            return 'Database connection failed: ' . $e->getMessage();
        }
    });


    /**
     *
     * Post
     */
    Route::get('/posts', [PostController::class, 'getSortedPosts']);
    Route::get('/posts/tag/{tagId}', [PostController::class, 'getSortedPostsByTag']);
    Route::get('/posts/search', [PostController::class, 'searchPostsByTitle']);
    Route::get('/posts/{id}', [PostController::class, 'getPostById']);
    Route::post('/posts/{postId}/increment-view', [PostController::class, 'incrementViewPost']);
    Route::get('/count/posts', [PostController::class, 'getCountPosts']);

    // Reactions to posts
    Route::get('/posts/{postId}/reactions', [PostController::class, 'getReactionsToPost']);
    Route::get('/posts/{postId}/reaction/{userId}', [PostController::class, 'getMyReactionIdToPost']);

    // Tags for posts
    Route::get('/posts/{postId}/tags', [PostController::class, 'getTagsByPostId']);

    // Number of posts
    Route::get('/count/posts/tag/{tagId}', [PostController::class, 'getCountPostsByTag']);
    Route::get('/count/posts/author/{userId}', [PostController::class, 'getCountPostsByAuthor']);


    /**
     *
     * Comment
     */
    Route::get('/comments/{id}', [CommentController::class, 'getCommentById']);
    Route::get('/comments/post/{post_id}', [CommentController::class, 'getFirstComments']);
    Route::get('/comments/{comment_id}/children', [CommentController::class, 'getFirstChildrensComment']);
    Route::get('/comments/{commentId}/reactions', [CommentController::class, 'getReactionsToComment']);
    Route::get('/comments/{commentId}/reaction/{userId}', [CommentController::class, 'getMyReactionIdToComment']);

    // Number of comments
    Route::get('/count/comments/{post_id}', [CommentController::class, 'getCountComments']);
    Route::get('/count/comments/author/{userId}', [CommentController::class, 'getCountCommentsByAuthor']);

    /**
     *
     * Reaction
     */
    Route::get('/reactions', [ReactionController::class, 'index']);

    /**
     *
     * Tag
     */
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/search', [TagController::class, 'search']);
    Route::get('/tags/{id}', [TagController::class, 'show']);
    Route::get('/tags/count/{userId}', [TagController::class, 'getCountTagsByAuthor']);
    Route::get('/tags/posts/{postId}', [TagController::class, 'getTagsByPostId']);

    /**
     *
     * User
     */
    Route::get('/users/search', [UserController::class, 'searchByName']);
    Route::get('/users/{id}', [UserController::class, 'getById']);
    Route::get('/users/uid/{uid}', [UserController::class, 'getByUid']);
    Route::put('/users/{id}', [UserController::class, 'updateById']);

    Route::middleware(['auth:sanctum'])->group(function ($router) {
        Route::post('/create/posts', [PostController::class, 'createPost']);

        Route::post('/tags', [TagController::class, 'create']);

        Route::post('/posts/reactions', [PostController::class, 'addReactionToPost']);
        Route::delete('/posts/reactions', [PostController::class, 'removeMyReactionToPost']);

        Route::post('/posts/tags', [PostController::class, 'addTagOnPost']);
        Route::delete('/posts/tags', [PostController::class, 'removeTagOnPost']);
        Route::put('/posts/tags', [PostController::class, 'changeTagsOnPost']);

        Route::post('/comments', [CommentController::class, 'createComment']);
        Route::put('/comments/{id}', [CommentController::class, 'updateCommentById']);

        Route::post('/comments/reactions', [CommentController::class, 'addReactionToComment']);
        Route::delete('/comments/reactions', [CommentController::class, 'removeMyReactionToComment']);

        Route::get('/user', function (Request $request) {
        return $request->user();
    });
    });
});

