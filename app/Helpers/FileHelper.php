<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function storeFile($user_id, $file)
    {
        try {
            // Get the current date
            $currentDate = now();

            // Create the directory structure
            $directory = "uploads/{$currentDate->year}/{$currentDate->month}/{$currentDate->day}/user/{$user_id}/";

            // Store the file
            $filePath = $directory . date('His') . '-' . $file->getClientOriginalName();

            Storage::disk('public')->put($filePath, file_get_contents($file));
        } catch (\Throwable $th) {
           $filePath = null;
        }

        // Return the stored file path
        return $filePath;
    }

    public static function deleteFile($filePath)
    {
        try {
            Storage::disk('public')->delete($filePath);
        } catch (\Throwable $th) {
            logger('Error: ' . __METHOD__ . ' -> ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
        }
    }
}