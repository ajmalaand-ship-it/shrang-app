<?php
namespace App\Services;
use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
class MediaService
{
    public function storeTemp(string $disk, string $path, string $mimeType, int $fileSize): MediaAsset
    {
        return MediaAsset::create([
            "storage_disk"     => $disk,
            "storage_key"      => $path,
            "mime_type"        => $mimeType,
            "file_size_bytes"  => $fileSize,
            "is_temp"          => true,
            "is_primary"       => false,
            "type"             => "uploaded_audio",
        ]);
    }
    public function signedDownloadUrl(MediaAsset $asset, int $minutesTtl = 60): string
    {
        if ($asset->cdn_url) {
            return $asset->cdn_url;
        }
        return \Illuminate\Support\Facades\Storage::disk($asset->storage_disk ?? "public")
            ->temporaryUrl($asset->storage_key, now()->addMinutes($minutesTtl));
    }
    public function publicUrl(MediaAsset $asset): string
    {
        if ($asset->cdn_url) {
            return $asset->cdn_url;
        }
        return Storage::disk($asset->storage_disk ?? "public")
            ->url($asset->storage_key);
    }
    public function primaryAssetForClip(string $clipId, string $type): ?MediaAsset
    {
        return MediaAsset::where("clip_id", $clipId)
            ->where("type", $type)
            ->where("is_primary", true)
            ->first();
    }
}
