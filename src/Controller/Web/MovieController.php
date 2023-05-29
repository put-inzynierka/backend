<?php

namespace App\Controller\Web;

use App\Controller\AbstractController;
use App\Entity\Movie\Movie;
use App\Repository\RepositoryFactory;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    #[Rest\Get(path: '/', name: 'homepage')]
    public function index(
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Movie::class);
        $movies = $repository->index()->getQuery()->getResult();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Rest\Get(
        path: '/movies/{id}',
        name: 'show_movie_web',
        requirements: ['id' => '\d+']
    )]
    #[ParamConverter(data: ['name' => 'movie'], class: Movie::class)]
    public function show(
        Movie $movie
    ): Response {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }
}
