<?php

namespace JaxWilko\MediaStream\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use JaxWilko\MediaStream\Models\MediaMeta;
use Log;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class Video extends ComponentBase
{
    const THUMBNAIL_TIMESTAMP = 2;

    const THUMBNAIL_EXTENSION = '-thumbnail.png';

    const DEFAULT_THUMBNAIL = '/plugins/jaxwilko/mediastream/assets/images/default.jpg';

    protected $file = null;

    protected $thumbnail;

    protected $meta;

    public function componentDetails()
    {
        return [
            'name' => 'MediaStream Video',
            'description' => 'Add a video to the page'
        ];
    }

    public function defineProperties()
    {
        return [
            'file' => [
                'title'             => 'Video File',
                'description'       => 'The file path in media of your video',
                'default'           => null,
                'type'              => 'string'
            ],
            'lazy' => [
                'title'             => 'Lazy load thumbnail',
                'description'       => 'Adds lazy to thumb class list and data-src instead of src',
                'default'           => false,
                'type'              => 'bool'
            ],
            'disable_meta' => [
                'title'             => 'Disable metadata',
                'description'       => 'Removes the schema data',
                'default'           => false,
                'type'              => 'bool'
            ]
        ];
    }

    public function onRun()
    {
        $this->page->addCss('/plugins/jaxwilko/mediastream/assets/css/video.css');
    }

    public function onRender()
    {
        $file = $this->property('file');

        if (!$file || !Storage::exists($file)) {
            Log::notice(sprintf('Video file `%s` not found', $file ?? 'undefined'));
            return;
        }

        $this->file = $file;

        if ($meta = MediaMeta::where('path', '/' . ltrim($this->property('file'), '/'))->first()) {
            $this->meta = $meta->data;

            if (isset($this->meta['thumbnail']) && $this->meta['thumbnail']) {
                $this->thumbnail = Storage::url(sprintf($this->mediaFormat, $this->meta['thumbnail']));
            }
        }
    }

    public function thumbnail(): string
    {
        return !$this->file
            ? static::DEFAULT_THUMBNAIL
            : $this->thumbnail ?? $this->thumbnail = $this->getThumbnail($this->file);
    }

    public function url(): string
    {
        return route('mediaStream', ['video' => base64_encode($this->property('file'))]);
    }

    public function lazy(): bool
    {
        return $this->property('lazy');
    }

    public function metaDisabled(): bool
    {
        return $this->property('disable_meta');
    }

    public function meta(string $key)
    {
        return $this->meta[$key] ?? null;
    }

    protected function getThumbnail(string $file): string
    {
        $resolvedPath = Storage::path($file);

        if (!File::exists($resolvedPath)) {
            // handle for remote filesystems where ffmpeg cannot reach
            return static::DEFAULT_THUMBNAIL;
        }

        $thumb = $resolvedPath . static::THUMBNAIL_EXTENSION;

        if (!Storage::exists($thumb)) {
            FFMpeg::create()
                ->open($resolvedPath)
                ->frame(TimeCode::fromSeconds(static::THUMBNAIL_TIMESTAMP))
                ->save($thumb);
        }

        return Storage::url($file . static::THUMBNAIL_EXTENSION);
    }
}
