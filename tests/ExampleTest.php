<?php

namespace Pbmengine\VideoApiClient\Tests;

use Pbmengine\VideoApiClient\PbmVideoApi;
use Pbmengine\VideoApiClient\Query\Builder;
use Pbmengine\VideoApiClient\Query\Serializer\JsonApiSerializer;
use Pbmengine\VideoApiClient\Resources\Project;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    protected $apiKey = 'cb3795aa674224f5ac644719d7aade02e6a79e91db90570f8e41aa90199b05f8e5ebfa132177c2aa384ac6d548f5240f';
    protected $accessKey = '0ad9ab3498b898a5d39d74bbf9815ec00864535f49f355ff14007e9fba40e98de18835cffd0bb37122fdaf7eb2835b97';
    protected $secretKey = 'e23bcbd7d489c084e2835f68d4124228586d944097ffb95e0ebb8a78c47c3fcb77039fe45edd4132976aa3068a856c06';
    protected $baseUrl = 'https://videos.api.dev.p-bm.com/v1/videoservice/';

    /** @var PbmVideoApi */
    protected $api;

    public function setUp(): void
    {
        $this->api = new PbmVideoApi(
            $this->baseUrl,
            $this->apiKey,
            $this->accessKey,
            $this->secretKey
        );
    }

    /** @test */
    public function test_project()
    {
        $builder = Builder::query()
            ->setSerializer(new JsonApiSerializer())
            ->take(100)
            ->get();

        $projects = $this->api
            ->query($builder)
            ->projects();

        /** @var $project Project */
        foreach ($projects as $project) {
            var_dump($project->videos());
        };

        $this->assertTrue(true);
    }
}
