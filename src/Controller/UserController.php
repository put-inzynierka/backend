<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\User\User;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Repository\SecurityUserRepository;
use App\Service\Instantiator;
use App\Service\User\PasswordHasher;
use App\Service\User\RegistrationService;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Rest\Get(path: '/users', name: 'index_users')]
    #[Tag('User')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of users',
        class: User::class,
        group: UserGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        SecurityUserRepository $repository
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: UserGroups::INDEX);
    }

    #[Rest\Get(
        path: '/users/{id}',
        name: 'show_user',
        requirements: ['id' => '\d+']
    )]
    #[Tag('User')]
    #[Param\Path('id', description: 'The ID of the user')]
    #[ParamConverter(data: ['name' => 'user'], class: User::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific user',
        class: User::class,
        group: UserGroups::SHOW,
    )]
    public function show(
        User $user
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $user);

        return $this->object($user, groups: UserGroups::SHOW);
    }

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
        Request $request,
        RegistrationService $registrationService,
    ): Response {
        /** @var User $user */
        $user = $instantiator->deserialize(
            $request->getContent(),
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

    #[Rest\Patch(
        path: '/users/{id}',
        name: 'update_user',
        requirements: ['id' => '\d+']
    )]
    #[Tag('User')]
    #[Param\Path('id', description: 'The ID of the user')]
    #[Param\Instance(User::class, UserGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'user'], class: User::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific user',
        class: User::class,
        group: UserGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        PasswordHasher $passwordHasher,
        User $user
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $user);

        $user = $instantiator->deserialize(
            $request->getContent(),
            User::class,
            UserGroups::UPDATE,
            $user
        );

        if (array_key_exists('password', json_decode($request->getContent()))) {
            $passwordHasher->hashPassword($user);
        }

        $manager->persist($user);
        $manager->flush();

        return $this->object($user, groups: UserGroups::SHOW);
    }
}
