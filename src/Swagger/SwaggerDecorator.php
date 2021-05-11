<?php

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['Oauth2Token'] = [
            'type' => 'object',
            'properties' => [
                'token_type' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'expires_in' => [
                    'type' => 'integer',
                    'readOnly' => true,
                    'examples' => [
                        3600,
                    ],
                ],
                'access_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['Oauth2Credentials'] = [
            'type' => 'object',
            'properties' => [
                'grant_type' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
                'client_id' => [
                    'type' => 'string',
                    'example' => 'app',
                ],
                'client_secret' => [
                    'type' => 'string',
                    'example' => '6255841cecc4189ca80f7c2a911bd465c76131069e88a2269140628bbc5b11e6d13e66ff041f4175052939292b20e6bb0acdb3b04203fcfdb6cf2beb147baecf',
                ],
                'username' => [
                    'type' => 'string',
                    'example' => 'john.doe@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'mySec.ret123!',
                ],
            ],
        ];

        $tokenDocumentation = [
            'paths' => [
                '/api/v1/oauth2/token' => [
                    'post' => [
                        'tags' => ['Oauth2'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get Oauth2 access token.',
                        'requestBody' => [
                            'description' => 'Create new Oauth2 Access Token',
                            'content' => [
                                'application/x-www-form-urlencoded' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Oauth2Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get Oauth2 Access token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Oauth2Token',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
