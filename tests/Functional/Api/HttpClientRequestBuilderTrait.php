<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait HttpClientRequestBuilderTrait
{
    private function httpRequestBuilder(KernelBrowser $client): HttpClientRequestBuilder
    {
        return new HttpClientRequestBuilder($client);
    }
}
