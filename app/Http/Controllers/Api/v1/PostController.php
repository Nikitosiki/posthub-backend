<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Post;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    // public function posts()
    // {
    //     return response()->json([
    //         'name' => 'John Doe',
    //         'email' => 'john.doe@example.com',
    //         'age' => 30
    //     ]);
    // }

    public function posts()
    {
        $posts = Post::all();
        return response()->json($posts);
    }
}
