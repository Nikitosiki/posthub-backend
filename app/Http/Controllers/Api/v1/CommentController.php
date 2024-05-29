<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Comment;
use App\Models\User;
use App\Models\Reaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;


class CommentController extends BaseController
{
    // Создание нового комментария
    public function createComment(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|uuid',
            'parent_comment_id' => 'nullable|bigint',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $comment = Comment::create([
            'content' => $validated['content'],
            'author_id' => $user->id,
            'post_id' => $validated['post_id'],
            'parent_comment_id' => $validated['parent_comment_id'],
        ]);

        return response()->json(['data' => $comment], 201);
    }

    // Обновление комментария по ID
    public function updateCommentById(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $content = $validated['content'];

        $comment = Comment::where('id', $id)
                          ->where('user_id', $user->id)
                          ->update(['content' => $content]);

        if ($comment > 0) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['error' => 'Comment not found or not authorized'], 404);
        }
    }

    // Получение комментария по ID
    public function getCommentById($id)
    {
        $comment = Comment::with(['author.gender', 'reactions'])->findOrFail($id);

        return response()->json($comment);
    }

    // Получение первых комментариев к посту
    public function getFirstComments(Request $request, $post_id)
    {
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 20);
        $inReverseOrder = $request->input('inReverseOrder', false);

        $comments = Comment::with(['author.gender', 'reactions'])
            ->where('post_id', $post_id)
            ->orderBy('path', $inReverseOrder ? 'desc' : 'asc')
            ->paginate($pageSize, ['*'], 'page', $pageNumber);

        return response()->json($comments);
    }

    // Получение количества комментариев к посту
    public function getCountComments($post_id)
    {
        $count = Comment::where('post_id', $post_id)->count();

        return response()->json(['count' => $count]);
    }

    // Получение первых дочерних комментариев
    public function getFirstChildrensComment(Request $request, $comment_id)
    {
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 20);
        $inReverseOrder = $request->input('inReverseOrder', false);

        $comments = Comment::where('parent_comment_id', $comment_id)
            ->orderBy('path', $inReverseOrder ? 'desc' : 'asc')
            ->paginate($pageSize, ['*'], 'page', $pageNumber);

        return response()->json($comments);
    }

    // Получение количества комментариев по автору
    public function getCountCommentsByAuthor($userId)
    {
        $count = Comment::where('author_id', $userId)->count();

        return response()->json(['count' => $count]);
    }

    // Добавление реакции к комментарию
    public function addReactionToComment(Request $request)
    {
        $validated = $request->validate([
            'comment_id' => 'required|bigint',
            'user_id' => 'required|uuid',
            'reaction_id' => 'required|integer',
        ]);

        // Удаление старой реакции
        DB::table('comment_reactions')
            ->where('comment_id', $validated['comment_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        // Добавление новой реакции
        $reaction = DB::table('comment_reactions')->insert($validated);

        return response()->json(['data' => $reaction], 201);
    }

    // Удаление своей реакции к комментарию
    public function removeMyReactionToComment(Request $request)
    {
        $validated = $request->validate([
            'comment_id' => 'required|bigint',
            'user_id' => 'required|uuid',
        ]);

        $result = DB::table('comment_reactions')
            ->where('comment_id', $validated['comment_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        return response()->json(['success' => $result > 0]);
    }

    // Получение реакций к комментарию
    public function getReactionsToComment($commentId)
    {
        $comment = Comment::with('reactions')->findOrFail($commentId);

        return response()->json($comment->reactions);
    }

    // Получение ID своей реакции к комментарию
    public function getMyReactionIdToComment($commentId, $userId)
    {
        $reaction = DB::table('comment_reactions')
            ->where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->select('reaction_id')
            ->first();

        return response()->json(['reaction_id' => $reaction ? $reaction->reaction_id : null]);
    }
}
