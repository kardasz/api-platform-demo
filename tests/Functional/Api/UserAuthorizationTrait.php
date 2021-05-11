<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Trikoder\Bundle\OAuth2Bundle\Model\Client as OAuth2Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;

trait UserAuthorizationTrait
{
    protected function createOauthClient(?string $identifier = null, ?string $secret = null, string $grant = 'password')
    {
        $manager = static::$container->get('trikoder.oauth2.manager.doctrine.client_manager');
        $manager->save(
            (new OAuth2Client($identifier ?? getenv('OAUTH2_CLIENT_ID'), $secret ?? getenv('OAUTH2_CLIENT_SECRET')))
                ->setActive(true)
                ->setGrants(new Grant($grant))
        );
    }

    protected function createOAuthToken(
        KernelBrowser $client,
        string $userName,
        string $password,
        ?string $identifier = null,
        ?string $secret = null,
        string $grant = 'password'
    ): ?string {
        $client->request('POST', '/api/v1/oauth2/token', [
            'grant_type' => $grant,
            'client_id' => $identifier ?? getenv('OAUTH2_CLIENT_ID'),
            'client_secret' => $secret ?? getenv('OAUTH2_CLIENT_SECRET'),
            'username' => $userName,
            'password' => $password,
        ]);

        $json = json_decode($client->getResponse()->getContent(), true);

        return $json['access_token'] ?? null;
    }
}
