<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

final class FileUpload
{
    protected string $path;

    protected array $paths;

    protected bool $secure = false;

    /**
     * @param  string  $path
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->paths = explode('/', $this->path);
    }

    /**
     * @param  string  $path
     * @return string
     */
    private static function normalize($path)
    {
        $path = preg_replace('#/+#', '/', trim($path, '/'));
        $parts = explode('/', $path);

        foreach ($parts as $key => $value) {
            $parts[$key] = Str::of($value)
                ->snake()
                ->slug()
                ->toString();
        }

        return implode('/', $parts);
    }

    /**
     * @param  string  $path
     * @return \App\Helpers\FileUpload
     */
    public static function parse($path)
    {
        return new self($path);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return Storage::exists($this->path);
    }

    /**
     * @param  \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    public function authenticate($user)
    {
        if (! $user) {
            return false;
        }

        [$directory, $model, $owner] = $this->paths;

        return (int) $owner === $user->id;
    }

    /**
     * @return string
     */
    public function file()
    {
        return Storage::path($this->path);
    }

    /**
     * @param  string  $name
     * @return string
     */
    public static function directory($name, User|Authenticatable|null $user = null)
    {
        if (preg_match('/^App\\\\Models\\\\/', $name)) {
            $name = basename(str_replace('\\', '/', $name));
        }

        $path = self::normalize('file-upload/'.$name);

        if ($user) {
            $path = $path.'/'.$user->id;
        }

        return $path;
    }

    /**
     * @param  File|UploadedFile  $file
     * @param  string | null  $path
     * @param  array  $options
     * @return string
     */
    public static function put($file, $path = null, $options = [])
    {
        $settings = [
            'image_width' => null,
            'image_height' => null,
            'image_quality' => 75,
            'image_resize_breakpoint' => 1080,
        ];

        foreach ($options as $key => $value) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = $value;
            }
        }

        if (! $file->isValid()) {
            throw new \Exception('Invalid file');
        }

        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $tid = time();
        $uid = uniqid();
        $filename = "{$tid}_{$uid}.{$extension}";

        $path = trim('/'.$path, '/').'/';
        $fullpath = $path.$filename;

        if (preg_match('#^image/#', $file->getClientMimeType())) {
            $manager = new ImageManager(new Driver);
            $image = $manager->read($file->getRealPath());

            $imgresizebp = $settings['image_resize_breakpoint'];

            $imgwidth = $imgresizebp < $image->width()
                ? $imgresizebp
                : $settings['image_width'];

            $image->scaleDown($imgwidth, $settings['image_height']);

            $encoded = $image->encode(new AutoEncoder(quality: $settings['image_quality']))->toString();
            Storage::put($fullpath, $encoded);
        } else {
            Storage::putFileAs($path, $file, $filename);
        }

        return $fullpath;
    }
}
