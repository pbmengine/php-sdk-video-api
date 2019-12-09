<?php

namespace Pbmengine\VideoApiClient\Resources;

class Project extends Resource
{
    public function update()
    {
        return $this->dto->toArray();
    }

    public function delete()
    {

    }
}
