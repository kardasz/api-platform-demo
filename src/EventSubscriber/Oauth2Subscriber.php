<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class Oauth2Subscriber.
 *
 * Adding predefined client_id and client_secret to the token request.
 */
class Oauth2Subscriber implements EventSubscriberInterface
{
    private ?string $clientId;
    private ?string $clientSecret;

    /**
     * Oauth2Subscriber constructor.
     */
    public function __construct(?string $clientId, ?string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        if ('oauth2_token' !== $request->attributes->get('_route')) {
            return;
        }

        $data = $request->request->all();
        if (!isset($data['client_id'], $data['client_secret'])) {
            $request->request->add(
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            );

            return;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
