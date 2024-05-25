<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Reaction;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ReactionController extends BaseController
{
    // Получение всех реакций
    public function getAllReactions()
    {
        $reactions = Reaction::all();
        return response()->json($reactions);
    }
}
