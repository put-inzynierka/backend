<?php

namespace App\EventListener;

use App\Repository\SecurityUserRepository;
use League\Bundle\OAuth2ServerBundle\Event\TokenRequestResolveEvent;

class TokenRequestListener
{
    public function __construct(
        protected SecurityUserRepository $userRepository,
    ) {}

    public function onTokenRequestResolve(TokenRequestResolveEvent $event)
    {
        $response = $event->getResponse();
        dd($response->getContent());
    }
}
