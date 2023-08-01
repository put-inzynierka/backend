<?php

namespace App\Controller\Movie;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Movie\Movie;
use App\Entity\Movie\Role;
use App\Enum\SerializationGroup\Movie\RoleGroups;
use App\Helper\Paginator;
use App\Repository\RoleRepository;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class RoleController extends AbstractController
{
    #[Rest\Get(
        path: '/movies/{id}/cast',
        name: 'index_movie_cast',
        requirements: ['id' => '\d+'],
    )]
    #[Tag('Movie')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the movie',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    #[Resp\PageResponse(
        description: 'Returns the list of cast for the movie',
        class: Role::class,
        group: RoleGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RoleRepository        $repository,
        Movie                 $movie
    ): Response {
        $list = $repository->indexByMovie($movie);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: RoleGroups::INDEX);
    }

    #[Rest\Get(
        path: '/movies/{movie_id}/cast/{id}',
        name: 'show_movie_cast_member',
        requirements: [
            'movie_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Movie')]
    #[Param\Path(
        name: 'movie_id',
        description: 'The ID of the movie',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the role',
    )]
    #[ParamConverter(data: ['name' => 'role'], class: Role::class)]
    #[Resp\ObjectResponse(
        description: 'Shows the specific cast member of the movie',
        class: Role::class,
        group: RoleGroups::SHOW,
    )]
    public function show(
        Role $role
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $role);

        return $this->object($role, groups: RoleGroups::SHOW);
    }

    #[Rest\Post(
        path: '/movies/{id}/cast',
        name: 'store_movie_cast_member',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Movie')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the movie',
    )]
    #[Param\Instance(Role::class, RoleGroups::CREATE)]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    #[Resp\ObjectResponse(
        description: 'Creates a new cast member for the movie',
        class: Role::class,
        group: RoleGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Movie $movie
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $movie);

        /** @var Role $role */
        $role = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Role::class,
            RoleGroups::CREATE
        );
        $role->setMovie($movie);

        $manager->persist($role);
        $manager->flush();

        return $this->object(
            $role,
            201,
            RoleGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/movies/{movie_id}/cast/{id}',
        name: 'update_movie_cast_member',
        requirements: [
            'movie_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Movie')]
    #[Param\Path(
        name: 'movie_id',
        description: 'The ID of the movie',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the cast member',
    )]
    #[Param\Instance(Role::class, RoleGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class, options: ['id' => 'movie_id'])]
    #[ParamConverter(data: ['name' => 'role'], class: Role::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific cast member for the movie',
        class: Role::class,
        group: RoleGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Movie $movie,
        Role $role
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $movie);
        $role = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Role::class,
             RoleGroups::UPDATE,
            $role
        );

        $manager->persist($role);
        $manager->flush();

        return $this->object($role, groups: RoleGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/movies/{movie_id}/cast/{id}',
        name: 'remove_movie_cast_member',
        requirements: [
            'movie_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Movie')]
    #[Param\Path(
        name: 'movie_id',
        description: 'The ID of the movie',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the cast member',
    )]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class, options: ['id' => 'movie_id'])]
    #[ParamConverter(data: ['name' => 'role'], class: Role::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific cast member from the movie',
    )]
    public function remove(
        EntityManagerInterface $manager,
        Movie $movie,
        Role $role
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $movie);
        $manager->remove($role);
        $manager->flush();

        return $this->empty();
    }
}
