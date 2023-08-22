<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Team\Team;
use App\Enum\SerializationGroup\Team\TeamGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use App\Service\Team\TeamCreator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

final class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamCreator $creator
    )
    {
    }

    #[Rest\Get(path: '/teams', name: 'index_teams')]
    #[Tag('Team')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of teams',
        class: Team::class,
        group: TeamGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory     $repositoryFactory
    ): Response
    {
        $repository = $repositoryFactory->create(Team::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: TeamGroups::INDEX);
    }

    #[Rest\Get(
        path: '/teams/{id}',
        name: 'show_team',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path('id', description: 'The ID of the team')]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific team',
        class: Team::class,
        group: TeamGroups::SHOW,
    )]
    public function show(
        Team $team
    ): Response
    {
        return $this->object($team, groups: TeamGroups::SHOW);
    }

    #[Rest\Post(path: '/teams', name: 'store_team')]
    #[Tag('Team')]
    #[Param\Instance(Team::class, TeamGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new team',
        class: Team::class,
        group: TeamGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        Request      $request
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Team $team */
        $team = $instantiator->deserialize(
            $request->getContent(),
            Team::class,
            TeamGroups::CREATE
        );

        $this->creator->create($team, $this->getUser());

        return $this->object(
            $team,
            201,
            TeamGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/teams/{id}',
        name: 'update_team',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path('id', description: 'The ID of the team')]
    #[Param\Instance(Team::class, TeamGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific team',
        class: Team::class,
        group: TeamGroups::SHOW,
    )]
    public function update(
        Instantiator           $instantiator,
        Request                $request,
        EntityManagerInterface $manager,
        Team                   $team
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

        $team = $instantiator->deserialize(
            $request->getContent(),
            Team::class,
            TeamGroups::UPDATE,
            $team
        );

        $manager->persist($team);
        $manager->flush();

        return $this->object($team, groups: TeamGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/teams/{id}',
        name: 'remove_team',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path('id', description: 'The ID of the team')]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific team',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Team                   $team
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

        $manager->remove($team);
        $manager->flush();

        return $this->empty();
    }
}