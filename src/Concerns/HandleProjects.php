<?php

namespace Pbmengine\VideoApiClient\Concerns;

use Pbmengine\VideoApiClient\DTO\ProjectData;
use Pbmengine\VideoApiClient\Resources\Project;

trait HandleProjects
{
    public function projects(): array
    {
        $response = $this->handleResponse(
            $this->getClient()->get('projects')
        );

        return $this->transformCollection(
            $response->contentAsArray()['data'],
            Project::class,
            ProjectData::class
        );
    }

    public function project($id): Project
    {
        $response = $this->handleResponse(
            $this->getClient()->get("projects/{$id}")
        );

        return new Project(
            ProjectData::fromApiResponse($response->contentAsArray()['data']),
            $this
        );
    }
}
