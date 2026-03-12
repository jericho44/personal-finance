<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Image
{
    /**
     * Store uploaded image with optional rotation and compression.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  int  $quality
     * @param  bool  $rotate
     * @return string|false
     */
    public static function store($file, $path, $quality = 75, $rotate = false)
    {
        return static::storeAs($file, $path, null, $quality, $rotate);
    }

    /**
     * Store named uploaded image with optional rotation and compression.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  string|null  $name
     * @param  int  $quality
     * @param  bool  $rotate
     * @return string|false
     */
    public static function storeAs($file, $path, $name = null, $quality = 75, $rotate = false)
    {
        if (! $file->isValid()) {
            return false;
        }

        $realpath = $file->getRealPath();
        $mimetype = $file->getMimeType();

        [$image, $extension] = self::createImageResource($realpath, $mimetype, $rotate);
        if (! $image) {
            return false;
        }

        $image = self::resizeIfNeeded($image, $extension);

        $name = $name ?: self::generateName($extension);
        $path = self::buildPath($path, $name);

        Storage::makeDirectory(dirname($path));

        $contents = self::encodeImageToString($image, $extension, $quality);
        imagedestroy($image);

        return $contents && Storage::put($path, $contents) ? $path : false;
    }

    /**
     * Create image resource based on MIME type and rotate if needed.
     *
     * @param  string  $realpath
     * @param  string  $mimetype
     * @param  bool  $rotate
     * @return array [resource|false, string|null]
     */
    private static function createImageResource($realpath, $mimetype, $rotate)
    {
        $image = false;
        $extension = null;

        switch ($mimetype) {
            case 'image/jpeg':
            case 'image/jpg':
                $extension = 'jpg';
                $image = imagecreatefromjpeg($realpath);
                if ($rotate && function_exists('exif_read_data')) {
                    $image = self::rotateImageFromExif($image, $realpath);
                }
                break;

            case 'image/png':
                $extension = 'png';
                $image = imagecreatefrompng($realpath);
                imagepalettetotruecolor($image);
                break;
        }

        return [$image, $extension];
    }

    /**
     * Rotate image based on EXIF orientation.
     *
     * @param  \GdImage|resource  $image
     * @param  string  $realpath
     * @return \GdImage|resource
     */
    private static function rotateImageFromExif($image, $realpath)
    {
        $exif = @exif_read_data($realpath);
        if (! empty($exif['Orientation'])) {
            switch ((int) $exif['Orientation']) {
                case 3:
                    return imagerotate($image, 180, 0);
                case 6:
                    return imagerotate($image, -90, 0);
                case 8:
                    return imagerotate($image, 90, 0);
            }
        }

        return $image;
    }

    /**
     * Resize image if width > 1080px.
     *
     * @param  \GdImage|resource  $image
     * @param  string  $extension
     * @return \GdImage|resource
     */
    private static function resizeIfNeeded($image, $extension)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= 1080) {
            return $image;
        }

        $newWidth = 1080;
        $newHeight = (int) ($height * ($newWidth / $width));
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if ($extension === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }

    /**
     * Generate random filename.
     *
     * @param  string  $extension
     * @return string
     */
    private static function generateName($extension)
    {
        $name = uniqid('ic_', true);
        $name = str_replace('.', '_', $name);

        return $name.'.'.$extension;
    }

    /**
     * Build full path with filename.
     *
     * @param  string  $basePath
     * @param  string  $name
     * @return string
     */
    private static function buildPath($basePath, $name)
    {
        $basePath = trim($basePath, '/');

        return $basePath.'/'.$name;
    }

    /**
     * Encode image into string using appropriate format and quality.
     *
     * @param  \GdImage|resource  $image
     * @param  string  $extension
     * @param  int  $quality
     * @return string|false
     */
    private static function encodeImageToString($image, $extension, $quality)
    {
        $quality = max(0, min(100, $quality));
        ob_start();
        $success = false;

        switch ($extension) {
            case 'jpg':
                $success = imagejpeg($image, null, $quality);
                break;
            case 'png':
                $pngQuality = (int) round(9 - ($quality / 100 * 9));
                $success = imagepng($image, null, $pngQuality);
                break;
        }

        $contents = ob_get_clean();

        return $success && $contents ? $contents : false;
    }
}
