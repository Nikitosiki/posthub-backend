<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Post;
use App\Models\Tag;
use App\Models\PostTag;
use App\Models\CountViewsAuth;
use App\Models\CountViewsUnauth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    // Создание нового поста
    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|uuid',
            'age_rating_id' => 'nullable|integer',
            'image_path' => 'nullable|string',
            'count_view' => 'nullable|integer'
        ]);

        $post = Post::create($validated);

        return response()->json(['data' => $post], 201);
    }

    // Получение отсортированных постов
    public function getSortedPosts(Request $request)
    {
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 10);
        $sortBy = $request->input('sortBy', 'new');

        $query = Post::with(['author.gender', 'tags.author.gender', 'reactions']);

        if ($sortBy === 'hot') {
            $query->orderBy('count_view', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate($pageSize, ['*'], 'page', $pageNumber);

        return response()->json($posts);
    }

    // Получение отсортированных постов по тегу
    public function getSortedPostsByTag(Request $request, $tagId)
    {
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 10);
        $sortBy = $request->input('sortBy', 'new');

        $postsId = DB::table('post_tags')
            ->where('tag_id', $tagId)
            ->select('post_id')
            ->pluck('post_id');

        $query = Post::whereIn('id', $postsId)
            ->with(['author.gender', 'tags.author.gender', 'reactions']);

        if ($sortBy === 'hot') {
            $query->orderBy('count_view', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate($pageSize, ['*'], 'page', $pageNumber);

        return response()->json($posts);
    }

    // Поиск постов по заголовку
    public function searchPostsByTitle(Request $request)
    {
        $text = $request->input('text', '');
        $limit = $request->input('limit', 5);

        $posts = Post::with(['author.gender', 'tags.author.gender', 'reactions'])
            ->where('title', 'ilike', '%' . $text . '%')
            ->limit($limit)
            ->get();

        return response()->json($posts);
    }

    // Получение поста по ID
    public function getPostById($id)
    {
        $post = Post::with(['author.gender', 'tags.author.gender', 'reactions'])
            ->findOrFail($id);

        return response()->json($post);
    }

    // Увеличение счетчика просмотров поста
    public function incrementViewPost(Request $request, $postId)
    {
        $identifier = $request->input('identifier');

        if (is_string($identifier)) {
            CountViewsUnauth::create([
                'post_id' => $postId,
                'fingerprint_id' => $identifier,
            ]);
        } else {
            CountViewsAuth::create([
                'post_id' => $postId,
                'user_id' => $identifier['id'],
            ]);
        }

        return response()->json(['message' => 'View incremented']);
    }

    // Получение количества постов
    public function getCountPosts()
    {
        $count = Post::count();

        return response()->json(['count' => $count]);
    }

    // Получение количества постов по тегу
    public function getCountPostsByTag($tagId)
    {
        $count = DB::table('post_tags')
            ->where('tag_id', $tagId)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Получение количества постов по автору
    public function getCountPostsByAuthor($userId)
    {
        $count = Post::where('author_id', $userId)->count();

        return response()->json(['count' => $count]);
    }

    // Добавление реакции к посту
    public function addReactionToPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'user_id' => 'required|uuid',
            'reaction_id' => 'required|integer',
        ]);

        // Удаление старой реакции
        DB::table('post_reactions')
            ->where('post_id', $validated['post_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        // Добавление новой реакции
        $reaction = DB::table('post_reactions')->insert($validated);

        return response()->json(['data' => $reaction], 201);
    }

    // Удаление своей реакции к посту
    public function removeMyReactionToPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'user_id' => 'required|uuid',
        ]);

        $result = DB::table('post_reactions')
            ->where('post_id', $validated['post_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        return response()->json(['success' => $result > 0]);
    }

    // Получение реакций к посту
    public function getReactionsToPost($postId)
    {
        $post = Post::with('reactions')->findOrFail($postId);

        return response()->json($post->reactions);
    }

    // Получение ID своей реакции к посту
    public function getMyReactionIdToPost($postId, $userId)
    {
        $reaction = DB::table('post_reactions')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->select('reaction_id')
            ->first();

        return response()->json(['reaction_id' => $reaction ? $reaction->reaction_id : null]);
    }

    // Добавление тега к посту
    public function addTagOnPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'tag_id' => 'required|uuid',
        ]);

        $postTag = PostTag::create($validated);

        return response()->json(['data' => $postTag], 201);
    }

    // Удаление тега с поста
    public function removeTagOnPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'tag_id' => 'required|uuid',
        ]);

        $result = PostTag::where('post_id', $validated['post_id'])
            ->where('tag_id', $validated['tag_id'])
            ->delete();

        return response()->json(['success' => $result > 0]);
    }

    // Добавление нескольких тегов к посту
    public function addTagsOnPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'tags' => 'required|array',
            'tags.*' => 'uuid',
        ]);

        $tagsId = $validated['tags'];
        $postId = $validated['post_id'];

        $results = [];
        foreach ($tagsId as $tagId) {
            $results[] = PostTag::create(['post_id' => $postId, 'tag_id' => $tagId]);
        }

        return response()->json(['data' => $results], 201);
    }

    // Изменение тегов у поста
    public function changeTagsOnPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid',
            'tags' => 'required|array',
            'tags.*' => 'uuid',
        ]);

        $tagsId = $validated['tags'];
        $postId = $validated['post_id'];

        // Удаляем существующие теги
        PostTag::where('post_id', $postId)->delete();

        // Добавляем новые теги
        $results = [];
        foreach ($tagsId as $tagId) {
            $results[] = PostTag::create(['post_id' => $postId, 'tag_id' => $tagId]);
        }

        return response()->json(['data' => $results], 201);
    }

    // Получение тегов по ID поста
    public function getTagsByPostId($postId)
    {
        $tags = Tag::whereHas('posts', function ($query) use ($postId) {
            $query->where('post_id', $postId);
        })->get();

        return response()->json(['data' => $tags]);
    }
}
