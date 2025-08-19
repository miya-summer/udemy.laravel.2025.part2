<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    public static function upload($imageFile, $folderName) {
        $fileName = uniqid(rand().'_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName . '.' . $extension;

        // 講座はIntervent Imageのv2なので、v3の書き方に変えてみた。もっとベストな方法があるかも！？
        $manager = new ImageManager(new Driver());

        $resizedImage = $manager->read($imageFile)
            ->resize(1920, 1080)
            ->encode();

        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);

        return $fileNameToStore;
    }
}
