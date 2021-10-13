<?php

namespace JaxWilko\MediaStream\Classes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;

class Stream
{
    const BUFFER_SIZE = 102400;

    protected $mediaFormat;

    public function __construct()
    {
        $this->mediaFormat = sprintf('/%s/%%s', config('cms.storage.media.folder'));
    }

    public function make(string $video)
    {
        $path = sprintf($this->mediaFormat, base64_decode($video));

        if (!Storage::exists($path)) {
            return \App::abort(404);
        }

        $mime = Storage::mimeType($path);

        if ((explode('/', $mime)[0] ?? null) !== 'video') {
            return \App::abort(404);
        }

        $size = Storage::size($path);
        $start = $streamStart = 0;
        $end = $streamEnd = $size - 1;
        $responseCode = 200;

        $stream = Storage::readStream($path);

        $headers = [
            'Content-Type'          => $mime,
            'Content-Length'        => $size,
            'Content-Disposition'   => 'attachment; filename="' . basename($path) . '"',
            'Last-Modified'         => gmdate('D, d M Y H:i:s', Storage::lastModified($path)) . ' GMT',
            'Expires'               => gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT',
            'Accept-Ranges'         => '0-' . $end
        ];

        if (Request::server('HTTP_RANGE')) {
            list($type, $range) = explode('=', Request::server('HTTP_RANGE'), 2);

            if (strpos($range, ',') !== false) {
                return Response::make('Requested Range Not Satisfiable', 416, [
                    'Content-Range' => sprintf('bytes %d-%d/%d', $start, $end, $size)
                ]);
            }

            if ($range !== '-') {
                $range = explode('-', $range);
                $streamStart = $range[0] ?? 0;
                $streamEnd = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $streamEnd;
            }

            $streamEnd = ($streamEnd > $end) ? $end : $streamEnd;

            if ($streamStart > $streamEnd || $streamStart > $size - 1 || $streamEnd >= $size) {
                return Response::make('Requested Range Not Satisfiable', 416, [
                    'Content-Range' => sprintf('bytes %d-%d/%d', $start, $end, $size)
                ]);
            }

            $length = $streamEnd - $streamStart + 1;
            fseek($stream, $streamStart);

            $responseCode = 206;

            $headers['Content-Length'] = $length;
            $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $streamStart, $streamEnd, $size);
        }

        return Response::stream(function() use ($stream, $streamStart, $streamEnd) {
            try {
                $i = $streamStart;
                set_time_limit(0);
                while (!feof($stream) && $i <= $streamEnd) {
                    $bytesToRead = static::BUFFER_SIZE;
                    if (($i + $bytesToRead) > $streamEnd) {
                        $bytesToRead = $streamEnd - $i + 1;
                    }
                    $data = fread($stream, $bytesToRead);
                    echo $data;
                    flush();
                    $i += $bytesToRead;
                }
            } catch(\Throwable $e) {
                \Log::error($e);
            } finally {
                fclose($stream);
            }
        }, $responseCode, $headers);
    }
}
