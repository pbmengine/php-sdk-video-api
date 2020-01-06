<?php

namespace Pbmengine\VideoApiClient\Concerns;

use Pbmengine\VideoApiClient\DTO\ProjectData;
use Pbmengine\VideoApiClient\Resources\Project;

trait HandleProjects
{
    public function projects(): array
    {
        $response = $this->handleResponse(
            $this->getClient()->get('projects' . $this->getQueryString())
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

        return $this->transformItem(
            $response->contentAsArray()['data'],
            Project::class,
            ProjectData::class);
    }

    public function updateProject($id, array $data)
    {
        $response = $this->handleResponse(
            $this->getClient()
                ->jsonPayload($data)
                ->put("projects/{$id}")
        );

        return $this->transformItem(
            $response->contentAsArray()['data'],
            Project::class,
            ProjectData::class);
    }

    public function deleteProject($id)
    {
        $this->handleResponse(
            $this->getClient()->delete("projects/{$id}")
        );

        return $id;
    }
}
