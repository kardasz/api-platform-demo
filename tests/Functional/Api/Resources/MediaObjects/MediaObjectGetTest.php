<?php

namespace App\Tests\Functional\Api\Resources\MediaObjects;

use ApiTestCase\JsonApiTestCase;
use App\Entity\MediaObject;
use App\Entity\User;
use App\Tests\Functional\Api\RequestBuilderTrait;
use App\Tests\Functional\Api\UserAuthorizationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaObjectGetTest extends JsonApiTestCase
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

    public function dataProvider()
    {
        return [
            [Response::HTTP_OK, 'MediaObjects/media_object_get_response', 'user.1@example.com', 'tokenUser1'],
            [Response::HTTP_OK, 'MediaObjects/media_object_get_response', 'user.1@example.com', 'tokenManager1'],
            [Response::HTTP_OK, 'MediaObjects/media_object_get_response', 'user.1@example.com', 'tokenAdmin1'],
            [Response::HTTP_FORBIDDEN, 'forbidden_response', 'user.1@example.com', 'tokenUser2'],
            [Response::HTTP_FORBIDDEN, 'forbidden_response', 'user.1@example.com', 'tokenManager2'],
        ];
    }

    /** @dataProvider dataProvider */
    public function testGetMediaObject(
        int $expectedStatusCode,
        string $expectedResponse,
        string $email,
        string $token
    ) {
        $mediaObjectId = $this->createMediaObject($email);
        $response = $this->getMediaObject(
            $mediaObjectId,
            self::$$token
        );
        $this->assertResponse(
            $response,
            $expectedResponse,
            $expectedStatusCode
        );
    }

    private function getMediaObject(string $id, string $token): Response
    {
        return $this->requestBuilder($this->client)
            ->withMethod(Request::METHOD_GET)
            ->withUri('/api/v1/media_objects/'.$id)
            ->withServer([
                 'HTTP_ACCEPT' => 'application/ld+json',
                 'HTTP_AUTHORIZATION' => 'Bearer '.$token,
             ])
            ->request();
    }

    private function createMediaObject(string $owner): string
    {
        $mediaObject = new MediaObject();
        $mediaObject->setFileName('example.doc');
        $mediaObject->setOwner(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['email' => $owner])
        );

        $this->getEntityManager()->persist($mediaObject);
        $this->getEntityManager()->flush();

        return $mediaObject->getId();
    }
}
