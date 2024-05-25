<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    // Поиск пользователей по имени
    public function searchByName(Request $request)
    {
        $name = $request->input('name');
        $limit = $request->input('limit', 50);

        $users = User::with('gender')->where('name', 'ilike', "%$name%")
            ->limit($limit)
            ->get();

        return response()->json($users);
    }

    // Получение пользователя по ID
    public function getById($id)
    {
        $user = User::with('gender')->find($id);

        return $user ? response()->json($user) : response()->json([], 404);
    }

    // Получение пользователя по UID
    public function getByUid($uid)
    {
        $user = User::with('gender')->where('uid', $uid)->first();

        return $user ? response()->json($user) : response()->json([], 404);
    }

    // Обновление пользователя по ID
    public function updateById(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->name = $request->input('name', $user->name);
        $user->avatar_url = $request->input('imageUrl', $user->avatar_url);

        $user->save();

        return response()->json(['success' => true]);
    }
}
