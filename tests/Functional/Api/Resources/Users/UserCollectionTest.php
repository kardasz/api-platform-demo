<?php

namespace App\Tests\Functional\Api\Resources\Users;

use ApiTestCase\JsonApiTestCase;
use App\Tests\Functional\Api\RequestBuilderTrait;
use App\Tests\Functional\Api\UserAuthorizationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCollectionTest extends JsonApiTestCase
{
    use RequestBuilderTrait;
    use UserAuthorizationTrait;

    private static $userToken;
    private static $managerToken;
    private static $adminToken;

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
            'manager.2@example.com',
            'SecretPassword123!'
        );

        self::$adminToken = $this->createOAuthToken(
            $this->client,
            'admin.1@example.com',
            'SecretPassword123!'
        );
    }

    public function dataProvider()
    {
        return [
            [Response::HTTP_OK, 'Users/user_manager_2_collection_response', 'managerToken'],
            [Response::HTTP_OK, 'Users/user_admin_1_collection_response', 'adminToken'],
            [Response::HTTP_FORBIDDEN, 'forbidden_response', 'userToken'],
        ];
    }

    /** @dataProvider dataProvider */
    public function testUserCollection(
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
            ->withUri('/api/v1/users')
            ->withServer([
                 'HTTP_ACCEPT' => 'application/ld+json',
                 'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ])
            ->request();
    }
}
