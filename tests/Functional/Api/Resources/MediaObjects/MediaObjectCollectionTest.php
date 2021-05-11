<?php

namespace App\Tests\Functional\Api\Resources\MediaObjects;

use ApiTestCase\JsonApiTestCase;
use App\Tests\Functional\Api\RequestBuilderTrait;
use App\Tests\Functional\Api\UserAuthorizationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaObjectCollectionTest extends JsonApiTestCase
{
    use RequestBuilderTrait;
    use UserAuthorizationTrait;

    private static $userToken;
    private static $managerToken;

    protected function setUp(): void
    {
        $this->purgeDatabase();
        $this->loadFixturesFromFiles(
            [
                'Users/users.yml',
                'MediaObjects/media_objects.yml',
                'OAuth2/client.yml',
            ]
        );

        self::$userToken = $this->createOAuthToken(
            $this->client,
            'user.1@example.com',
            'SecretPassword123!'
        );

        self::$managerToken = $this->createOAuthToken(
            $this->client,
            'manager.1@example.com',
            'SecretPassword123!'
        );
    }

    public function dataProvider()
    {
        return [
            [Response::HTTP_OK, 'MediaObjects/media_object_user_1_collection_response', 'userToken'],
            [Response::HTTP_OK, 'MediaObjects/media_object_manager_1_collection_response', 'managerToken'],
        ];
    }

    /** @dataProvider dataProvider */
    public function testMediaObjectCollection(
        int $expectedStatusCode,
        string $expectedResponse,
        string $token
    ) {
        $response = $this->request(self::$$token);
        $this->assertResponse(
            $response,
            $expectedResponse,
            $expectedStatusCode
        );
    }

    private function request(string $token): Response
    {
        return $this->requestBuilder($this->client)
            ->withMethod(Request::METHOD_GET)
            ->withUri('/api/v1/media_objects')
            ->withServer([
                 'HTTP_ACCEPT' => 'application/ld+json',
                 'HTTP_AUTHORIZATION' => 'Bearer '.$token,
             ])
            ->request();
    }
}
