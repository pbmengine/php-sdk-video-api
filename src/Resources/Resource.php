<?php

namespace Pbmengine\VideoApiClient\Resources;

use Pbmengine\VideoApiClient\PbmVideoApi;
use Spatie\DataTransferObject\DataTransferObject;

abstract class Resource
{
    /** @var DataTransferObject */
    protected $dto;

    /** @var PbmVideoApi */
    protected $api;

    public function __construct(DataTransferObject $dto, PbmVideoApi $api)
    {
        $this->dto = $dto;
        $this->api = $api;
    }

    public function data(): DataTransferObject
    {
        return $this->dto;
    }
}
