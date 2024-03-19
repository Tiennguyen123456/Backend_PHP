<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;
use Image;

class Helper
{
    public static function checkEmailForm($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDateTimeFormat($dateTime)
    {
        return Carbon::parse($dateTime)->format(config('app.date_format'));
    }

    public static function getDateFormat($date)
    {
        return date(config('app.date_only_format'), strtotime($date));
    }

    public static function tableHasColumn($tableName, $columnName)
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateQrCode($data)
    {
        try {
            $qrDir = config('app.qr_code.dir');

            File::ensureDirectoryExists($qrDir, 0777, true, true);

            $filePath = $qrDir . Str::random(10) . '.png';

            QrCode::format('png')->generate($data, storage_path($filePath));

            return $filePath;
        } catch (\Throwable $th) {
            logger($th->getMessage());
            return null;
        }
    }
}
