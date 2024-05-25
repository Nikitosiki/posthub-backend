<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Tag;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TagController extends BaseController
{
    // Создание нового тега
    public function create(Request $request)
    {
        $tag = Tag::create($request->all());
        return response()->json($tag, 201);
    }

    // Получение отсортированных тегов
    public function index(Request $request)
    {
        $sortBy = $request->input('sortBy', 'latest');
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 10);

        $tags = Tag::with(['users' => function($query) {
            $query->with('genders');
        }])->orderBy($sortBy === 'latest' || $sortBy === 'first' ? 'created_at' : 'title', $sortBy === 'first' || $sortBy === 'ascending' ? 'asc' : 'desc')
        ->skip(($pageNumber - 1) * $pageSize)
        ->take($pageSize)
        ->get();

        return response()->json($tags);
    }

    // Поиск тегов по заголовку
    public function search(Request $request)
    {
        $title = $request->input('title');
        $limit = $request->input('limit', 50);

        $tags = Tag::with(['users' => function($query) {
            $query->with('genders');
        }])->where('title', 'ilike', "%$title%")
        ->limit($limit)
        ->get();

        return response()->json($tags);
    }

    // Получение ID тега по заголовку
    public function getTagIdByTitle(Request $request)
    {
        $title = $request->input('title');
        $tag = Tag::where('title', $title)->first();
        return $tag ? $tag->id : null;
    }

    // Получение тега по ID
    public function show($id)
    {
        $tag = Tag::with(['users' => function($query) {
            $query->with('genders');
        }])->find($id);

        return $tag ? response()->json($tag) : response()->json([], 404);
    }

    // Получение количества тегов по автору
    public function getCountTagsByAuthor($userId)
    {
        $count = Tag::where('author_id', $userId)->count();
        return response()->json($count);
    }

    // Получение тегов по ID поста
    public function getTagsByPostId($postId)
    {
        $tags = Tag::with(['users' => function($query) {
            $query->with('genders');
        }])->whereHas('posts', function($query) use ($postId) {
            $query->where('id', $postId);
        })->get();

        return response()->json($tags);
    }
}
