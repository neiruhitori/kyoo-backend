<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\StoreFavorite;
use App\Favorite;
use Auth;

class FavoriteController extends Controller
{

    public function index()
    {
        $favorites = Favorite::with('Branch')->whereUserId(Auth::id())->get()->pluck('branch');
        return response()->json([
            'success' => true,
            'message' => 'get all favorite',
            'data' => $favorites
        ]);
    }

    public function store(StoreFavorite $request)
    {
        $is_available = Favorite::whereUserIdAndBranchId($request->user_id, $request->branch_id)->first();
        if ($is_available) {
            return response()->json([
            'success' => false,
            'message' => 'this user already add branch to favorite',
            'data' => null
        ]);
        }
        $favorite = Favorite::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'insert favorite success',
            'data' => $favorite
        ]);
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();
        return response()->json([
            'success' => true,
            'message' => 'remove favorite success',
            'data' => $favorite
        ]);
    }
}
