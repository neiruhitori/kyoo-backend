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
        $favorites = Favorite::with('Service.Branch')->whereUserId(Auth::id())->get();
        return response()->json([
            'success' => true,
            'message' => 'get all favorite',
            'data' => $favorites
        ]);
    }

    public function store(StoreFavorite $request)
    {
        $favorite = Favorite::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'insert favorite success',
            'data' => $favorite
        ]);
    }
}
