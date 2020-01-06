<?php

namespace Pbmengine\VideoApiClient\Resources;

use Pbmengine\VideoApiClient\DTO\VideoData;

class Video extends Resource
{
    /** @var VideoData */
    protected $dto;

    public function delete()
    {
        return $this->api->deleteVideo($this->data()->id);
    }

    public function template()
    {
        return $this->api->template($this->data()->template_id, $this->data()->project_id);
    }
}
