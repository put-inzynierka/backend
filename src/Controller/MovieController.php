<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Movie\Movie;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    #[Rest\Get(path: '/api/movies', name: 'index_movies')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of movies',
        class: Movie::class,
        group: MovieGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Movie::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: MovieGroups::INDEX);
    }

    #[Rest\Get(
        path: '/api/movies/{id}',
        name: 'show_movie',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the movie')]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific movie',
        class: Movie::class,
        group: MovieGroups::SHOW,
    )]
    public function show(
        Movie $movie
    ): Response {
        return $this->object($movie, groups: MovieGroups::SHOW);
    }

    #[Rest\Post(path: '/api/movies', name: 'store_movie')]
    #[Param\Instance(Movie::class, MovieGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new movie',
        class: Movie::class,
        group: MovieGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Movie $movie */
        $movie = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Movie::class,
            MovieGroups::CREATE
        );

        $manager->persist($movie);
        $manager->flush();

        return $this->object(
            $movie,
            201,
            MovieGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/api/movies/{id}',
        name: 'update_movie',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the movie')]
    #[Param\Instance(Movie::class, MovieGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific movie',
        class: Movie::class,
        group: MovieGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Movie $movie
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $movie);

        $movie = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Movie::class,
            MovieGroups::UPDATE,
            $movie
        );

        $manager->persist($movie);
        $manager->flush();

        return $this->object($movie, groups: MovieGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/api/movies/{id}',
        name: 'remove_movie',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the movie')]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific movie',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Movie $movie
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $movie);

        $manager->remove($movie);
        $manager->flush();

        return $this->empty();
    }
}
