<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\User\User;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Service\Instantiator;
use App\Service\User\RegistrationService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Rest\Post(path: '/user', name: 'store_user')]
    #[Tag('User')]
    #[Param\Instance(User::class, UserGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new user',
        class: User::class,
        group: UserGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        RegistrationService $registrationService,
    ): Response {
        /** @var User $user */
        $user = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            User::class,
            UserGroups::CREATE
        );

        $registrationService->register($user);

        return $this->object(
            $user,
            201,
            UserGroups::SHOW
        );
    }
}
