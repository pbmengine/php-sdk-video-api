<?php

namespace Pbmengine\VideoApiClient;

use Pbmengine\Restclient\HttpClient;
use Pbmengine\Restclient\HttpResponse;
use Pbmengine\VideoApiClient\Concerns\HandleProjects;

class PbmVideoApi
{
    use HandleProjects;

    /** @var string */
    protected $basePath;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $accessKey;

    /** @var string */
    protected $secretKey;

    public function __construct($basePath, $apiKey, $accessKey = null, $secretKey = null)
    {
        $this->basePath = $basePath;
        $this->apiKey = $apiKey;
        $this->accessKey = is_null($accessKey) ? config('pbm-video-api.access_key') : $accessKey;
        $this->secretKey = is_null($secretKey) ? config('pbm-video-api.secret_key') : $secretKey;
    }

    protected function getClient(): HttpClient
    {
        $client = new HttpClient();

        $client
            ->baseUrl($this->basePath)
            ->headers($this->getAuthHeaders())
            ->option('http_errors', false)
            ->option('verify', false);

        return $client;
    }

    public function secretKey($key): self
    {
        $this->secretKey = $key;

        return $this;
    }

    public function accessKey($key): self
    {
        $this->accessKey = $key;

        return $this;
    }

    public function destinations(): HttpResponse
    {
        return $this
            ->getClient()
            ->get('destinations');
    }

    protected function getAuthHeaders(): array
    {
        return [
            'x-api-key' => $this->apiKey,
            'x-access-key' => $this->accessKey,
            'x-secret-key' => $this->secretKey,
        ];
    }
}
