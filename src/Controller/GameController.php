<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Game\Game;
use App\Enum\SerializationGroup\Game\GameGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    #[Rest\Get(path: '/games', name: 'index_games')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of games',
        class: Game::class,
        group: GameGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Game::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: GameGroups::INDEX);
    }

    #[Rest\Get(
        path: '/games/{id}',
        name: 'show_game',
        requirements: ['id' => '\d+']
    )]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class)]
    #[Resp\ObjectResponse(
        description: 'Returns the specific game',
        class: Game::class,
        group: GameGroups::SHOW,
    )]
    public function show(
        Game $game
    ): Response {
        return $this->object($game, groups: GameGroups::SHOW);
    }

    #[Rest\Post(path: '/games', name: 'store_game')]
    #[Param\Instance(Game::class, GameGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new game',
        class: Game::class,
        group: GameGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Game $game */
        $game = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Game::class,
            GameGroups::CREATE
        );
        $game->setOwner($this->getUser());

        $manager->persist($game);
        $manager->flush();

        return $this->object(
            $game,
            201,
            GameGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/games/{id}',
        name: 'update_game',
        requirements: ['id' => '\d+']
    )]
    #[Param\Instance(Game::class, GameGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific game',
        class: Game::class,
        group: GameGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Game $game
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);

        $game = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Game::class,
            GameGroups::UPDATE,
            $game
        );

        $manager->persist($game);
        $manager->flush();

        return $this->object($game, groups: GameGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/games/{id}',
        name: 'remove_game',
        requirements: ['id' => '\d+']
    )]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific game',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Game $game
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);

        $manager->remove($game);
        $manager->flush();

        return $this->empty();
    }
}
