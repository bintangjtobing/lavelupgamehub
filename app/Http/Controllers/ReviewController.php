<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
            'agree_terms' => 'accepted',
        ]);

        // Create a new review
        Review::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'agree_terms' => TRUE,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Terima kasih atas review Anda!');
    }
}
