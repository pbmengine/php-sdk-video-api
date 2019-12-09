<?php

namespace Pbmengine\VideoApiClient\DTO;

use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class ProjectData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $identifier;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $access_key;

    /** @var string */
    public $secret_key;

    /** @var Carbon */
    public $created_at;

    /** @var Carbon */
    public $updated_at;

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
