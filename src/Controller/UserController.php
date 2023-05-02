<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\User\User;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Rest\Post(path: '/user', name: 'store_user')]
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
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var User $user */
        $user = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            User::class,
            UserGroups::CREATE
        );

        $password = $user->getPassword();
        $password = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        return $this->object(
            $user,
            201,
            UserGroups::SHOW
        );
    }
}
