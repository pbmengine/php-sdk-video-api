<?php

namespace Pbmengine\VideoApiClient;

use Pbmengine\Restclient\HttpClient;
use Pbmengine\Restclient\HttpResponse;
use Pbmengine\VideoApiClient\Concerns\HandleDestinations;
use Pbmengine\VideoApiClient\Concerns\HandleProjects;
use Pbmengine\VideoApiClient\Concerns\HandleTemplates;
use Pbmengine\VideoApiClient\Concerns\HandleVideos;
use Pbmengine\VideoApiClient\Exceptions\NotFoundException;
use Pbmengine\VideoApiClient\Exceptions\ServerException;
use Pbmengine\VideoApiClient\Exceptions\ValidationException;
use Pbmengine\VideoApiClient\Query\Builder;

class PbmVideoApi
{
    use HandleProjects,
        HandleTemplates,
        HandleVideos,
        HandleDestinations;

    /** @var string */
    protected $basePath;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $accessKey;

    /** @var string */
    protected $secretKey;

    /** @var array|null */
    protected $queryParams;

    /** @var string|null */
    protected $queryString;

    public function __construct($basePath, $apiKey, $accessKey, $secretKey)
    {
        $this->basePath = $basePath;
        $this->apiKey = $apiKey;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
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

    public function setSecretKey($key): self
    {
        $this->secretKey = $key;

        return $this;
    }

    public function setAccessKey($key): self
    {
        $this->accessKey = $key;

        return $this;
    }

    protected function getAuthHeaders(): array
    {
        return [
            'x-api-key'    => $this->apiKey,
            'x-access-key' => $this->accessKey,
            'x-secret-key' => $this->secretKey,
        ];
    }

    protected function transformCollection(array $collection, $class, $dto): array
    {
        return array_map(function ($data) use ($class, $dto) {
            return new $class($dto::fromApiResponse($data), $this);
        }, $collection);
    }

    protected function transformItem(array $item, $class, $dto)
    {
        return new $class($dto::fromApiResponse($item), $this);
    }

    protected function handleResponse(HttpResponse $response): HttpResponse
    {
        if ($response->isValid()) {
            return $response;
        }

        $this->handleResponseError($response);
    }

    protected function handleResponseError(HttpResponse $response)
    {
        if ($response->statusCode() == 404) {
            throw new NotFoundException($response->contentAsJson());
        }

        if ($response->statusCode() == 422) {
            throw new ValidationException($response->contentAsJson());
        }

        if ($response->isServerError()) {
            throw new ServerException($response->contentAsJson());
        }

        throw new \Exception('error');
    }

    public function query(Builder $builder)
    {
        $this->queryString = $builder->toString();

        return $this;
    }

    protected function getQueryString()
    {
        if ($this->queryString == null) {
            return '';
        }

        return '?' . $this->queryString;
    }
}
