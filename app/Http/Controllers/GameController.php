<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        $items = CatalogItem::active()
            ->when($query, fn ($builder) => $builder->where('name', 'like', '%'.$query.'%'))
            ->orderBy('name')
            ->get(['name', 'slug', 'cover', 'type', 'variant']);

        // image_url dan game_url adalah accessor, jadi dirakit di sini
        return response()->json($items->map(fn ($item) => [
            'name' => $item->name,
            'slug' => $item->slug,
            'image_url' => $item->image_url,
            'game_url' => $item->game_url,
            'type' => $item->type,
        ]));
    }
}
