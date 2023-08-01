<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Movie\Genre;
use App\Enum\SerializationGroup\Movie\GenreGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class GenreController extends AbstractController
{
    #[Rest\Get(path: '/genres', name: 'index_genres')]
    #[Tag('Genre')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of genres',
        class: Genre::class,
        group: GenreGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Genre::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: GenreGroups::INDEX);
    }

    #[Rest\Get(
        path: '/genres/{id}',
        name: 'show_genre',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Genre')]
    #[Param\Path('id', description: 'The ID of the genre')]
    #[ParamConverter(data: ['name' => 'genre'], class: Genre::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific genre',
        class: Genre::class,
        group: GenreGroups::SHOW,
    )]
    public function show(
        Genre $genre
    ): Response {
        return $this->object($genre, groups: GenreGroups::SHOW);
    }

    #[Rest\Post(path: '/genres', name: 'store_genre')]
    #[Tag('Genre')]
    #[Param\Instance(Genre::class, GenreGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new genre',
        class: Genre::class,
        group: GenreGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Genre $genre */
        $genre = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Genre::class,
            GenreGroups::CREATE
        );

        $manager->persist($genre);
        $manager->flush();

        return $this->object(
            $genre,
            201,
            GenreGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/genres/{id}',
        name: 'update_genre',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Genre')]
    #[Param\Path('id', description: 'The ID of the genre')]
    #[Param\Instance(Genre::class, GenreGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'genre'], class: Genre::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific genre',
        class: Genre::class,
        group: GenreGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Genre $genre
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $genre);

        $genre = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Genre::class,
            GenreGroups::UPDATE,
            $genre
        );

        $manager->persist($genre);
        $manager->flush();

        return $this->object($genre, groups: GenreGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/genres/{id}',
        name: 'remove_genre',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Genre')]
    #[Param\Path('id', description: 'The ID of the genre')]
    #[ParamConverter(data: ['name' => 'genre'], class: Genre::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific genre',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Genre $genre
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $genre);

        $manager->remove($genre);
        $manager->flush();

        return $this->empty();
    }
}
