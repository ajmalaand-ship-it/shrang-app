<?php

namespace App\Http\Controllers;

use App\Models\Clip;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $clips = Clip::where('visibility', 'public')
            ->where('status', 'ready')
            ->whereNotNull('slug')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        $content = view('sitemap', compact('clips'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
