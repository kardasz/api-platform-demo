<?php

namespace App\Tests\Functional\Api\Resources\MediaObjects;

use ApiTestCase\JsonApiTestCase;
use App\Tests\Functional\Api\RequestBuilderTrait;
use App\Tests\Functional\Api\UserAuthorizationTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaObjectCreateTest extends JsonApiTestCase
{
    use RequestBuilderTrait;
    use UserAuthorizationTrait;

    private static $token;

    protected function setUp(): void
    {
        $this->purgeDatabase();
        $this->loadFixturesFromFiles(
            [
                'Users/users.yml',
                'OAuth2/client.yml',
            ]
        );

        self::$token = $this->createOAuthToken(
            $this->client,
            'user.1@example.com',
            'SecretPassword123!'
        );
    }

    public function testPostMediaObjectCreate()
    {
        $response = $this->requestBuilder($this->client)
            ->withMethod(Request::METHOD_POST)
            ->withUri('/api/v1/media_objects')
            ->withServer([
                 'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
                 'HTTP_ACCEPT' => 'application/ld+json',
                 'HTTP_AUTHORIZATION' => 'Bearer '.self::$token,
             ])
            ->withFile(
                'file',
                new UploadedFile(
                    __DIR__.'/Files/example.doc',
                    'example.doc',
                    'application/msword',
                    null
                )
            )
            ->request();

        $this->assertResponse(
            $response,
            'MediaObjects/media_object_create_response',
            Response::HTTP_CREATED
        );
    }
}
