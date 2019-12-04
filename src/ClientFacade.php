<?php

namespace Pbmengine\VideoApiClient;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pbmengine\VideoApiClient\PbmVideoApi
 */
class ClientFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pbm-video-api';
    }
}
