<?php

namespace Pbmengine\VideoApiClient\Concerns;

use Pbmengine\Restclient\HttpResponse;

trait HandleProjects
{
    public function projects(): HttpResponse
    {
        return $this
            ->getClient()
            ->get('projects');
    }
}
