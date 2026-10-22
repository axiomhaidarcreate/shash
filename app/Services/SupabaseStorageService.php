<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SupabaseStorageService
{
    /**
     * Upload a file to local public storage.
     * Falls back to local storage since Supabase is not configured.
     *
     * @param string $bucket   The storage bucket/folder name (e.g. 'avatars', 'attachments')
     * @param UploadedFile $file  The uploaded file
     * @param string $subfolder  Optional subfolder (e.g. 'employees', 'entitlements')
     * @return string  The public URL or storage path of the uploaded file
     */
    public static function upload(string $bucket, UploadedFile $file, string $subfolder = ''): string
    {
        $directory = $bucket . ($subfolder ? '/' . $subfolder : '');
        
        $path = $file->store($directory, 'public');

        return '/storage/' . $path;
    }
}
