<?php

namespace App\Tests\Functional\Api\Resources\Users;

use ApiTestCase\JsonApiTestCase;
use App\Entity\User;
use App\Tests\Functional\Api\RequestBuilderTrait;
use App\Tests\Functional\Api\UserAuthorizationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserNotifyTest extends JsonApiTestCase
{
    use RequestBuilderTrait;
    use UserAuthorizationTrait;

    private static $tokenUser1;
    private static $tokenUser2;
    private static $tokenManager1;
    private static $tokenManager2;
    private static $tokenAdmin1;

    protected function setUp(): void
    {
        $this->purgeDatabase();
        $this->loadFixturesFromFiles(
            [
                'Users/users.yml',
                'OAuth2/client.yml',
            ]
        );

        self::$tokenUser1 = $this->createOAuthToken(
            $this->client,
            'user.1@example.com',
            'SecretPassword123!'
        );

        self::$tokenUser2 = $this->createOAuthToken(
            $this->client,
            'user.2@example.com',
            'SecretPassword123!'
        );

        self::$tokenManager1 = $this->createOAuthToken(
            $this->client,
            'manager.1@example.com',
            'SecretPassword123!'
        );

        self::$tokenManager2 = $this->createOAuthToken(
            $this->client,
            'manager.2@example.com',
            'SecretPassword123!'
        );

        self::$tokenAdmin1 = $this->createOAuthToken(
            $this->client,
            'admin.1@example.com',
            'SecretPassword123!'
        );
    }

    public function notifyProvider()
    {
        return [
            [Response::HTTP_NO_CONTENT, 'user.1@example.com', 'tokenManager1'],
            [Response::HTTP_NO_CONTENT, 'user.1@example.com', 'tokenAdmin1'],
            [Response::HTTP_NO_CONTENT, 'user.2@example.com', 'tokenAdmin1'],
            [Response::HTTP_NO_CONTENT, 'user.2@example.com', 'tokenManager2'],
            [Response::HTTP_NO_CONTENT, 'user.3@example.com', 'tokenManager2'],
            [Response::HTTP_FORBIDDEN, 'user.2@example.com', 'tokenManager1'],
            [Response::HTTP_FORBIDDEN, 'user.1@example.com', 'tokenUser1'],
            [Response::HTTP_FORBIDDEN, 'user.1@example.com', 'tokenUser2'],
        ];
    }

    /** @dataProvider notifyProvider */
    public function testNotifyUser(
        int $expectedStatusCode,
        string $email,
        string $token
    ) {
        $userId = $this->getUserIdByEmail($email);
        $response = $this->notifyUser(
            $userId,
            self::$$token
        );

        $this->assertResponseCode(
            $response,
            $expectedStatusCode
        );
    }

    private function notifyUser(string $id, string $token): Response
    {
        return $this->requestBuilder($this->client)
            ->withMethod(Request::METHOD_POST)
            ->withUri('/api/v1/users/'.$id.'/notify')
            ->withServer([
                 'HTTP_ACCEPT' => 'application/ld+json',
                 'HTTP_AUTHORIZATION' => 'Bearer '.$token,
             ])
            ->request();
    }

    private function getUserIdByEmail(string $email): string
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $email])
            ->getId();
    }
}
