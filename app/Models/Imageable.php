<?php

namespace App\Models;

use App\Constants\GlobalConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Imageable extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return MorphTo
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Upload an image and return the filename.
     *
     * @param string $file Base64 encoded image file
     * @param string $path Destination path
     * @param int $key Unique key for file naming
     * @return string
     */
    public static function uploadImage(string $file, string $path, int $key = 0): string
    {
        $base64Str = substr($file, strpos($file, ",") + 1);
        $imageDecoded = base64_decode($base64Str);

        $imageFormat = self::getImageFormat($file);

        $fileName = sprintf('%s-%d-%s.%s', date('Y-m-d-H-i-s'), $key, Str::random(10), $imageFormat);

        if (env('UPLOAD_DRIVER') === 'AWS') {
            Storage::disk('s3')->put("public/{$path}/{$fileName}", $imageDecoded);
        } else {
            Storage::disk('public')->put("uploads/{$path}/{$fileName}", $imageDecoded);
        }

        return $fileName;
    }

    /**
     * Get the image format from the base64 string.
     *
     * @param string $file
     * @return string
     */
    private static function getImageFormat(string $file): string
    {
        if (strpos($file, ';') !== false) {
            $imageFormat = explode(';', $file)[0];
            $imageFormat = explode('/', $imageFormat)[1];
            return $imageFormat === 'svg+xml' ? 'svg' : $imageFormat;
        }
        return GlobalConstants::DEFAULT_IMAGE_FORMAT;
    }

    /**
     * Get the full image path.
     *
     * @param string $dir
     * @param string|null $image
     * @return string
     */
    public static function getImagePath(string $dir, ?string $image): string
    {
        if (is_null($image) || Str::contains($image, 'image-not-found')) {
            return self::getImageNotFound();
        }

        $baseUrl = (env('UPLOAD_RESIZE') === true)
            ? self::baseUrlResized()
            : self::baseUrl();

        return $baseUrl . $dir . '/' . $image;
    }

    /**
     * Get multiple file URLs.
     *
     * @param string $dir
     * @param array $files
     * @param string $name
     * @return array
     */
    public static function getMultipleFiles(string $dir, array $files, string $name = 'url'): array
    {
        return array_map(function ($file) use ($dir, $name) {
            return [$name => (env('UPLOAD_RESIZE') === true ? self::baseUrlResized() : self::baseUrl()) . "{$dir}/{$file->url}"];
        }, $files);
    }

    /**
     * Get if the image path contains the driver
     *
     * @return string
     */
    public static function contains(): string
    {
        return (env('UPLOAD_RESIZE') === true)
            ? self::baseUrlResized()
            : self::baseUrl();
    }

    /**
     * Get the default image not found URL.
     *
     * @return string
     */
    public static function getImageNotFound(): string
    {
        return (env('UPLOAD_DRIVER') === 'AWS')
            ? env('AWS_BUCKET') . '/image-not-found.jpeg'
            : request()->root() . '/image-not-found.jpeg';
    }


    /**
     * Get the base url path.
     *
     * @return string
     */
    public static function baseUrl(): string
    {
        return (env('UPLOAD_DRIVER') === 'AWS')
            ? env('AWS_BUCKET')
            : request()->root() . '/uploads/';
    }

    /**
     * Get the base url resized path.
     *
     * @return string
     */
    public static function baseUrlResized(): string
    {
        return (env('UPLOAD_DRIVER') === 'AWS')
            ? env('AWS_BUCKET_RESIZED')
            : request()->root() . '/uploads/';
    }
}
