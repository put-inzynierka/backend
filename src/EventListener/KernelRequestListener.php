<?php

namespace App\EventListener;

use App\Entity\User\User;
use League\Bundle\OAuth2ServerBundle\Security\Authenticator\OAuth2Authenticator;
use League\Bundle\OAuth2ServerBundle\Security\Exception\OAuth2AuthenticationFailedException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class KernelRequestListener
{
    public function __construct(
        protected OAuth2Authenticator $authenticator,
        protected TokenStorageInterface $tokenStorage
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        try {
            $passport = $this->authenticator->doAuthenticate($event->getRequest());
        } catch (OAuth2AuthenticationFailedException $exception) {
            $this->tokenStorage->setToken(new NullToken());

            return;
        }

        /** @var User $user */
        $user = $passport->getUser();

        $token = new UsernamePasswordToken($user, 'user-firewall', []);
        $this->tokenStorage->setToken($token);
    }
}