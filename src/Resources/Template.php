<?php

namespace Pbmengine\VideoApiClient\Resources;

use Pbmengine\VideoApiClient\DTO\TemplateData;

class Template extends Resource
{
    /** @var TemplateData */
    protected $dto;

    public function update(array $data)
    {
        return $this->api->updateProject($this->dto->id, $data);
    }

    public function delete()
    {
        return $this->api->deleteProject($this->dto->id);
    }
}
