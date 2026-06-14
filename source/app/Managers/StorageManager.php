<?php

namespace App\Managers;

use App\Constants\StorageDisk;
use App\Constants\StorageUrl;
use Carbon\Carbon;
use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\WebM;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class StorageManager
{
    protected static int $maxSizeMB = 10;

    public static function setMaxSize(int $size): void
    {
        self::$maxSizeMB = $size;
    }

    private static function getStorageDisk(): string
    {
        return env('APP_ENV') === 'local' ? StorageDisk::PUBLIC : StorageDisk::S3;
    }

    public static function isSizeValid(UploadedFile $file): bool
    {
        return $file->getSize() <= (self::$maxSizeMB * 1024 * 1024);
    }

    public static function isImageValid(UploadedFile $file): bool
    {
        return in_array($file->getClientOriginalExtension(), ['webp', 'jpg', 'jpeg', 'png']);
    }

    public static function isVideoValid(UploadedFile $file): bool
    {
        return in_array($file->getClientOriginalExtension(), ['webm', 'mp4', 'avi', 'mov']);
    }

    public static function isAudioValid(UploadedFile $file): bool
    {
        return in_array(strtolower($file->getClientOriginalExtension()), ['webm', 'mp3', 'wav', 'ogg', 'm4a']);
    }

    public static function getImageFileName(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalName.'_'.TextManager::makeRandomString(8).'_'.Carbon::now()->format('YmdHis').'.webp';

        return $fileName;
    }

    public static function getVideoFileName(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalName.'_'.TextManager::makeRandomString(8).'_'.Carbon::now()->format('YmdHis').'.webm';

        return $fileName;
    }

    public static function saveImageAsWebp(UploadedFile $file, string $pathTemplate, array $replacements, array $dimensions = [], int $quality = 50): string
    {
        $fileName = self::getImageFileName($file);
        $fullPath = self::getDynamicPath($pathTemplate, $replacements, $fileName);

        $image = Image::read($file);

        if (! empty($dimensions) && isset($dimensions['width']) && isset($dimensions['height'])) {
            $image = self::processImage($image, $dimensions['width'], $dimensions['height']);
        }

        $image = $image->toWebp($quality);
        Storage::disk(self::getStorageDisk())->put($fullPath, $image);

        return config('filesystems.storage_url').'/'.$fullPath;
    }

    private static function processImage($image, int $width, int $height)
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        if ($originalWidth > $width) {
            $newWidth = $width;
            $newHeight = ($originalHeight * $newWidth) / $originalWidth;
        }

        if ($newHeight > $height) {
            $newHeight = $height;
            $newWidth = ($originalWidth * $newHeight) / $originalHeight;
        }

        return $image->resize(intval($newWidth), intval($newHeight));
    }

    public static function saveVideoAsWebm(UploadedFile $file, string $pathTemplate, array $replacements): string
    {
        $fileName = self::getVideoFileName($file);
        $fullPath = self::getDynamicPath($pathTemplate, $replacements, $fileName);
        $tempFilePath = $file->getPathname();

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('services.ffmpeg.ffmpeg_path'),
            'ffprobe.binaries' => config('services.ffmpeg.ffprobe_path'),
        ]);

        $convertedPath = tempnam(sys_get_temp_dir(), 'buddyscript_video_').'.webm';

        try {
            $video = $ffmpeg->open($tempFilePath);
            $format = new WebM;
            $video->save($format, $convertedPath);
            Storage::disk(self::getStorageDisk())->put($fullPath, file_get_contents($convertedPath));
        } catch (Exception $e) {
            throw new Exception('Video conversion failed: '.$e->getMessage());
        } finally {
            if (file_exists($convertedPath)) {
                unlink($convertedPath);
            }
        }

        return config('filesystems.storage_url').'/'.$fullPath;
    }

    private static function getDynamicPath(string $template, array $replacements, string $fileName): string
    {
        $replacements['{fileName}'] = $fileName;

        $path = str_replace(array_keys($replacements), array_values($replacements), $template);

        return str_replace('{fileName}', $fileName, $path);
    }

    public static function getUserImageUrl(int $userId, ?string $fileName): string
    {
        if ($fileName === null) {
            return config('filesystems.storage_url').'/icon/default/user_icon.png';
        }

        $path = self::getDynamicPath(StorageUrl::USER_IMAGE, ['{userId}' => $userId], $fileName);

        return config('filesystems.storage_url').'/'.$path;
    }

    public static function uploadFile(UploadedFile $file, string $pathTemplate, array $replacements): string
    {
        if (! self::isSizeValid($file)) {
            throw new FileException('File size exceeds the allowed limit of '.self::$maxSizeMB.'MB.');
        }

        $fileName = uniqid().'.'.$file->getClientOriginalExtension();
        $fullPath = self::getDynamicPath($pathTemplate, $replacements, $fileName);

        Storage::disk(self::getStorageDisk())->put($fullPath, file_get_contents($file));

        return config('filesystems.storage_url').'/'.$fullPath;
    }
}
