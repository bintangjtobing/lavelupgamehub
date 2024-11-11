<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        // Fetch games with name, image_url, and game_url based on query
        $games = $query
            ? Game::where('name', 'like', '%' . $query . '%')->select('name', 'image_url', 'game_url')->get()
            : Game::select('name', 'image_url', 'game_url')->get();

        return response()->json($games);
    }
}
