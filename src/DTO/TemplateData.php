<?php

namespace Pbmengine\VideoApiClient\DTO;

use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class TemplateData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $project_id;

    /** @var string */
    public $movie_name;

    /** @var string */
    public $movie_project_id;

    /** @var string */
    public $destination;

    /** @var string */
    public $video_format;

    /** @var string */
    public $image_format;

    /** @var array */
    public $params;

    /** @var int */
    public $parallel_servers;

    /** @var string */
    public $routing_key;

    /** @var boolean */
    public $protection;

    /** @var int */
    public $deletion_in_days;

    /** @var Carbon */
    public $created_at;

    public static function fromApiResponse(array $response)
    {
        return new self($response);
    }

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {

            if ($key == 'created_at' || $key == 'updated_at') {
                $value = Carbon::createFromTimestamp(strtotime($value));
            }

            $this->{$key} = $value;
        }
    }
}
