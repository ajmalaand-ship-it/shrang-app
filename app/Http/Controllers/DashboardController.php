<?php

namespace App\Http\Controllers;

use App\Models\Clip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filter = in_array($request->query('filter'), ['ready', 'failed', 'all']) ? $request->query('filter') : 'ready';

        $query = Clip::where('user_id', $request->user()->id)
            ->with(['mediaAssets' => function ($q) {
                $q->where('is_primary', true)
                  ->whereIn('type', ['song_audio', 'bed_audio', 'uploaded_audio', 'cover_image', 'reel_video']);
            }])
            ->orderByDesc('created_at');

        if ($filter === 'ready') {
            $query->where('status', 'ready');
        } elseif ($filter === 'failed') {
            $query->where('status', 'failed');
        }

        $clips = $query->paginate(20)->withQueryString();

        $langNames = [
            'ps' => 'Pashto', 'fa' => 'Dari', 'ur' => 'Urdu',
            'ar' => 'Arabic', 'hi' => 'Hindi', 'en' => 'English',
        ];

        return view('pages.dashboard.index', compact('clips', 'langNames', 'filter'));
    }
}
