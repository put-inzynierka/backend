<?php

namespace App\Controller\Game;

use App\Component\Attribute as Param;
use App\Controller\AbstractController;
use App\Entity\Game\Game;
use App\Entity\Game\Key;
use App\Enum\SerializationGroup\Game\KeyGroups;
use App\Helper\Paginator;
use App\Repository\KeyRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class KeyController extends AbstractController
{
    #[Rest\Get(
        path: '/games/{id}/keys',
        name: 'index_game_keys',
        requirements: ['id' => '\d+']
    )]
    #[Param\Limit]
    #[Param\Page]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class)]
    public function index(
        ParamFetcherInterface $paramFetcher,
        KeyRepository $repository,
        Game $game
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);
        $list = $repository->indexByGame($game);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: KeyGroups::INDEX);
    }

    #[Rest\Get(
        path: '/games/{game_id}/keys/{id}',
        name: 'show_game_key',
        requirements: [
            'game_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[ParamConverter(data: ['name' => 'key'], class: Key::class)]
    public function show(
        Key $key
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $key);
        return $this->object($key, groups: KeyGroups::SHOW);
    }

    #[Rest\Post(
        path: '/games/{id}/keys',
        name: 'store_game_key',
        requirements: ['id' => '\d+']
    )]
    #[Param\Instance]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class)]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Game $game
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);

        /** @var Key $key */
        $key = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Key::class,
            KeyGroups::CREATE
        );
        $key->setGame($game);

        $manager->persist($key);
        $manager->flush();

        return $this->object(
            $key,
            201,
            KeyGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/games/{game_id}/keys/{id}',
        name: 'update_game_key',
        requirements: [
            'game_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Param\Instance]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class, options: ['id' => 'game_id'])]
    #[ParamConverter(data: ['name' => 'key'], class: Key::class)]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Game $game,
        Key $key
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);
        $key = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Key::class,
            KeyGroups::UPDATE,
            $key
        );

        $manager->persist($key);
        $manager->flush();

        return $this->object($key, groups: KeyGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/games/{game_id}/keys/{id}',
        name: 'remove_game_key',
        requirements: [
            'game_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[ParamConverter(data: ['name' => 'game'], class: Game::class, options: ['id' => 'game_id'])]
    #[ParamConverter(data: ['name' => 'key'], class: Key::class)]
    public function remove(
        EntityManagerInterface $manager,
        Game $game,
        Key $key
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $game);
        $manager->remove($key);
        $manager->flush();

        return $this->empty();
    }
}