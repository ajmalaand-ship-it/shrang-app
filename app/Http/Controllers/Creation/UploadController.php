<?php
namespace App\Http\Controllers\Creation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "audio" => ["required", "file", "mimes:mp3,wav,aac,ogg", "max:51200"],
        ]);
        return response()->json(["status" => "pending", "message" => "Upload processing coming in Phase 6"]);
    }
}
