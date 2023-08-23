<?php

namespace App\EventListener;

use App\Enum\SerializationGroup\BaseGroups;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Repository\SecurityUserRepository;
use League\Bundle\OAuth2ServerBundle\Event\TokenRequestResolveEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;

class TokenRequestListener
{
    public function __construct(
        protected RequestStack $requestStack,
        protected SecurityUserRepository $userRepository,
        protected SerializerInterface $serializer
    ) {}

    public function onTokenRequestResolve(TokenRequestResolveEvent $event)
    {
        $response = json_decode($event->getResponse()->getContent(), true);
        $username = $this->requestStack->getCurrentRequest()->get('username');

        $user = $this->userRepository->getUserByIdentifier($username);
        $response['user'] = json_decode(
            $this->serializer->serialize($user, 'json', ['groups' => [BaseGroups::DEFAULT, UserGroups::SHOW]]),
            true
        );

        $event->getResponse()->setContent(json_encode($response));
    }
}
