<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserSignatureStorage
{
    public static function storeFromRequest(Request $request, string $fileKey, string $canvasKey, User $user): ?string
    {
        if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
            return self::storeUploadedFile($request->file($fileKey), $user);
        }

        $b64 = $request->input($canvasKey);
        if (self::isDataImage($b64)) {
            return self::storeDataUri($b64, $user);
        }

        return null;
    }

    public static function storeUploadedFile(UploadedFile $file, User $user): string
    {
        return $file->store(self::directoryFor($user), 'public');
    }

    public static function storeDataUri(string $dataUri, User $user): ?string
    {
        $parts = explode(',', $dataUri, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $raw = base64_decode($parts[1], true);
        if ($raw === false) {
            return null;
        }

        $filename = self::directoryFor($user) . '/' . uniqid('sig_', true) . '.png';
        Storage::disk('public')->put($filename, $raw);

        return $filename;
    }

    public static function dataUriForUser(User $user): ?string
    {
        if (! $user->signature_path) {
            return null;
        }

        $path = storage_path('app/public/' . $user->signature_path);
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($raw);
    }

    public static function deleteForUser(User $user): void
    {
        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }
    }

    public static function isDataImage(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, 'data:image');
    }

    private static function directoryFor(User $user): string
    {
        return 'signatures/profiles/' . $user->id;
    }
}
