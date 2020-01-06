<?php

namespace Pbmengine\VideoApiClient\Concerns;

use Pbmengine\VideoApiClient\DTO\VideoData;
use Pbmengine\VideoApiClient\Resources\Video;

trait HandleVideos
{
    public function videos($projectId): array
    {
        $response = $this->handleResponse(
            $this->getClient()->get("projects/{$projectId}/videos" . $this->getQueryString())
        );

        return $this->transformCollection(
            $response->contentAsArray()['data'],
            Video::class,
            VideoData::class
        );
    }

    public function video($id, $projectId): Video
    {
        $response = $this->handleResponse(
            $this->getClient()->get("projects/{$projectId}/videos/{$id}")
        );

        return $this->transformItem(
            $response->contentAsArray()['data'],
            Video::class,
            VideoData::class);
    }

    public function deleteVideo($id, $projectId)
    {
        $this->handleResponse(
            $this->getClient()->delete("projects/{$projectId}/videos/{$id}")
        );

        return $id;
    }
}
