<?php

namespace Pbmengine\VideoApiClient\DTO;

use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class VideoData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $project_id;

    /** @var string */
    public $template_id;

    /** @var string */
    public $status;

    /** @var string */
    public $status_message;

    /** @var string */
    public $storage_key;

    /** @var string */
    public $url;

    /** @var string */
    public $download_url;

    /** @var bool */
    public $is_public;

    /** @var int */
    public $duration;

    /** @var int */
    public $filesize;

    /** @var string */
    public $content_type;

    /** @var string */
    public $format;

    /** @var string */
    public $destination;

    /** @var string */
    public $codec;

    /** @var string */
    public $bitrate;

    /** @var string */
    public $audio_bitrate;

    /** @var string */
    public $audio_codec;

    /** @var string */
    public $frame_rate;

    /** @var int */
    public $frames;

    /** @var int */
    public $width;

    /** @var int */
    public $height;

    /** @var int */
    public $render_time;

    /** @var string|null */
    public $render_token;

    /** @var string */
    public $aspect_ratio;

    /** @var Carbon|null */
    public $deletion_at;

    /** @var Carbon */
    public $created_at;

    public static function fromApiResponse(array $response)
    {
        return new self($response);
    }

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {

            if ($key == 'created_at') {
                $value = Carbon::createFromTimestamp(strtotime($value));
            }

            if ($key == 'deletion_at' && $value !== null) {
                $value = Carbon::createFromTimestamp(strtotime($value));
            }

            $this->{$key} = $value;
        }
    }
}
