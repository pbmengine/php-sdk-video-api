<?php

namespace Pbmengine\VideoApiClient\Concerns;

use Pbmengine\VideoApiClient\DTO\TemplateData;
use Pbmengine\VideoApiClient\Resources\Template;

trait HandleTemplates
{
    public function templates($projectId): array
    {
        $response = $this->handleResponse(
            $this->getClient()->get("projects/{$projectId}/templates" . $this->getQueryString())
        );

        return $this->transformCollection(
            $response->contentAsArray()['data'],
            Template::class,
            TemplateData::class
        );
    }

    public function template($id, $projectId): Template
    {
        $response = $this->handleResponse(
            $this->getClient()->get("projects/{$projectId}/templates/{$id}")
        );

        return $this->transformItem(
            $response->contentAsArray()['data'],
            Template::class,
            TemplateData::class);
    }

    public function updateTemplate($id, $projectId, array $data): Template
    {
        $response = $this->handleResponse(
            $this->getClient()
                ->jsonPayload($data)
                ->put("projects/{$projectId}/templates/{$id}")
        );

        return $this->transformItem(
            $response->contentAsArray()['data'],
            Template::class,
            TemplateData::class);
    }

    public function deleteTemplate($id, $projectId)
    {
        $this->handleResponse(
            $this->getClient()->delete("projects/{$projectId}/templates/{$id}")
        );

        return $id;
    }
}
