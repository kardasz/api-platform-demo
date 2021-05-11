<?php

namespace App\Tests\Functional\Api\Resources\Users;

use ApiTestCase\JsonApiTestCase;
use App\Tests\Functional\Api\RequestBuilderTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSelfRegistrationTest extends JsonApiTestCase
{
    use RequestBuilderTrait;

    private array $server = [
        'CONTENT_TYPE' => 'application/ld+json',
        'HTTP_ACCEPT' => 'application/ld+json',
    ];

    protected function setUp(): void
    {
        $this->purgeDatabase();
        $this->loadFixturesFromFile('Users/users.yml');
    }

    public function dataProvider()
    {
        return [
            [Response::HTTP_CREATED, 'Users/self_registration_response', 'Users/self_registration_post.json'],
            [Response::HTTP_BAD_REQUEST, 'Users/self_registration_response_validation', 'Users/self_registration_post_invalid.json'],
            [Response::HTTP_BAD_REQUEST, 'Users/self_registration_response_password_validation', 'Users/self_registration_post_invalid_password.json'],
            [Response::HTTP_BAD_REQUEST, 'Users/self_registration_response_email_validation', 'Users/self_registration_post_invalid_email.json'],
        ];
    }

    /** @dataProvider dataProvider */
    public function testUserSelfRegistration(
        int $expectedStatusCode,
        string $expectedResponse,
        string $content
    ) {
        $response = $this->requestBuilder($this->client)
            ->withMethod(Request::METHOD_POST)
            ->withUri('/api/v1/users/self-registration')
            ->withServer($this->server)
            ->withContent($content)
            ->request();

        $this->assertResponse(
            $response,
            $expectedResponse,
            $expectedStatusCode
        );
    }
}
