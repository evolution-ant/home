<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiJokeController extends Controller
{
    function type(Request $request)
    {
        return response()->json([
            'posts' => [
                'description' => 'description',
                'href' => 'href',
            ],
        ]);
    }
}
