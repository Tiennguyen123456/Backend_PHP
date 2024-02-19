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
    public static function randomStringNoVn($length = 8)
    {
        $permittedChars = '0123456789bcghklmnpqrtvzBCGHKLMNPQRTVZ';

        $inputLength = strlen($permittedChars);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomCharacter = $permittedChars[mt_rand(0, $inputLength - 1)];
            $randomString .= $randomCharacter;
        }

        return $randomString;
    }

    public static function randomString($length = 8)
    {
        $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $inputLength = strlen($permittedChars);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomCharacter = $permittedChars[mt_rand(0, $inputLength - 1)];
            $randomString .= $randomCharacter;
        }

        return $randomString;
    }

    public static function generateQrcode($prefix)
    {
        $randomCode = strtoupper(self::randomStringNoVn(5));
        $time = date('dmyhis');
        $qrcode = "{$prefix}{$time}{$randomCode}";
        return $qrcode;
    }

    public static function generateShortQrcode($prefix = null)
    {
        $randomCode = strtoupper(self::randomStringNoVn(4));
        $time = date('his');

        if (!empty($prefix)) {
            $qrcode = "{$prefix}{$time}{$randomCode}";
        } else {
            $qrcode = "{$time}{$randomCode}";
        }

        return $qrcode;
    }

    public static function generateImgQrcode($qrcode, $folder = 'qrcode')
    {
        $folder = strtolower($folder);
        $path = "qrcodes/{$folder}";
        $folderPath = storage_path("app/{$path}");

        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath, 0700, true, true);
        }

        $tmpPath = "{$folderPath}/{$qrcode}_tmp.png";
        $mainPath = "{$folderPath}/{$qrcode}.png";

        $qrcodeGenerate = QrCode::format('png')
            ->size(300)
            ->backgroundColor(255, 255, 255)
            ->encoding('UTF-8');


        $logoPath = public_path("images/frontend/" . strtoupper($folder) . "/logo.png");

        if (File::exists($logoPath)) {
            $qrcodeGenerate = $qrcodeGenerate->merge($logoPath, .2, true)
                ->errorCorrection('Q');
        }

        $qrcodeGenerate = $qrcodeGenerate->generate($qrcode, $tmpPath);

        /* Generate white border */
        $qr = Image::make($tmpPath);
        $border = Image::canvas(350, 350, "#FFFFFF");
        $border->insert($qr, 'center');

        $border->save($mainPath);

        /* Remove tmp Qr code */
        File::delete($tmpPath);

        // return Storage::url("public/qrcodes/{$folder}/{$qrcode}.png"); // app/storage/app/qr
        return "{$path}/{$qrcode}.png";
    }

    public static function generateImgQrcodeWithCode($qrcode, $folder = 'qrcode')
    {
        $folder = strtolower($folder);
        $path = "qrcodes/{$folder}";
        $folderPath = storage_path("app/{$path}");

        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath, 0700, true, true);
        }

        $tmpPath = "{$folderPath}/{$qrcode}_tmp.png";
        $mainPath = "{$folderPath}/{$qrcode}.png";

        QrCode::format('png')
            ->size(300)
            ->backgroundColor(255, 255, 255)
            ->encoding('UTF-8')
            ->generate($qrcode, $tmpPath);

        /* Generate white border */
        $qr = Image::make($tmpPath);
        $border = Image::canvas(350, 380, "#FFFFFF");
        $border->insert($qr, 'center');

        /* Insert code text */
        $border->text($qrcode, $border->getWidth() / 2, $border->getHeight() - 20, function ($font) {
            $font->file(public_path('assets/fonts/toyota-type.ttf')); // Replace with the path to your font file
            $font->size(14); // Adjust the font size as needed
            $font->color('#000000'); // Adjust the font color as needed
            $font->align('center');
            $font->valign('bottom');
        });

        $border->save($mainPath);

        /* Remove tmp Qr code */
        File::delete($tmpPath);

        // return Storage::url("public/qrcodes/{$folder}/{$qrcode}.png"); // app/storage/app/qr
        return "{$path}/{$qrcode}.png";
    }

    public static function getValueJson($field, $customFields = null)
    {
        if (!empty($customFields)) {
            $fields = json_decode($customFields);
            return $fields->$field;
        }
        return null;
    }

    public static function getCurrentRouteAction()
    {
        $controllerAction = Route::currentRouteAction();
        $listAction = explode('\\', $controllerAction);
        list($controller, $action) = explode('@', $listAction[4]);

        $result = [
            'full' => "{$controller}.{$action}",
            'controller' => "{$controller}",
            'action' => "{$action}"
        ];

        return $result;
    }

    public static function removeSpaceOnStr($str, $isUpper = true, $stripVietnamese = true, $hasHyphen = false)
    {
        $str = trim($str);
        $newStr = '';

        if ($hasHyphen) {
            $str = str_replace(' ', '-', $str);
            $str = strtolower($str);
            $str = Str::ascii($str);

            if ($isUpper) {
                $str = Str::upper($str);
            }

            return $str;
        }

        $parts = explode(' ', $str);

        foreach ($parts as $part) {
            if ($isUpper) {
                $part = Str::upper($part);
            }

            if ($stripVietnamese) {
                $part = Str::ascii($part);
            }

            $newStr .= $part;
        }

        return $newStr;
    }

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

    public static function checkDateFormat($dateString)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);

        if ($date === false) {
            return false;
        }

        return $date->format('Y-m-d') === $dateString;
    }

    public static function compareDateToToday($dateString)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        $currentDate = new DateTime();

        if ($date > $currentDate) {
            return 1;
        } elseif ($date->format('Y-m-d') == $currentDate->format('Y-m-d')) {
            return 0;
        }

        return -1;
    }

    public static function tableHasColumn($tableName, $columnName)
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            return true;
        } else {
            return false;
        }
    }
}
