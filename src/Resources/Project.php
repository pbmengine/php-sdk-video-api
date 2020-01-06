<?php

namespace Pbmengine\VideoApiClient\Resources;

use Pbmengine\VideoApiClient\DTO\ProjectData;

class Project extends Resource
{
    /** @var ProjectData */
    protected $dto;

    public function update(array $data)
    {
        return $this->api->updateProject($this->data()->id, $data);
    }

    public function delete()
    {
        return $this->api->deleteProject($this->data()->id);
    }

    public function templates()
    {
        return $this->api->templates($this->data()->id);
    }

    public function videos()
    {
        return $this->api->videos($this->data()->id);
    }
}
