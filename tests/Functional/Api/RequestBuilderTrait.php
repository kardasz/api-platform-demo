<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait RequestBuilderTrait
{
    private function requestBuilder(KernelBrowser $client): RequestBuilder
    {
        return new RequestBuilder($client);
    }
}
