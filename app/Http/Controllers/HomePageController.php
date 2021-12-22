<?php

namespace App\Http\Controllers;

use App\Models\Songs\SongType;
use Inertia\Inertia;
use Inertia\Response;

class HomePageController extends Controller
{
    public function __invoke(): Response
    {
        $songTypes = SongType::with(['latestSongs.author', 'latestSongs.media'])->get();

        return Inertia::render('HomePage', compact('songTypes'));
    }
}
