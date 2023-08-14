<?php declare(strict_types=1);

namespace App\Controller\Team;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Enum\SerializationGroup\Team\TeamGroups;
use App\Enum\SerializationGroup\Team\TeamMemberGroups;
use App\Helper\Paginator;
use App\Repository\TeamMemberRepository;
use App\Service\Instantiator;
use App\Service\Team\TeamInvitationService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

final class TeamMemberController extends AbstractController
{
    public function __construct(
        private readonly TeamInvitationService $invitationService
    )
    {
    }

    #[Rest\Get(
        path: '/teams/{id}/team-members',
        name: 'index_team_team_members',
        requirements: ['id' => '\d+'],
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\PageResponse(
        description: 'Returns the list of members for given team',
        class: TeamMember::class,
        group: TeamMemberGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        TeamMemberRepository  $repository,
        Team                  $team
    ): Response
    {
        $list = $repository->indexByTeam($team);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: TeamMemberGroups::INDEX);
    }

    #[Rest\Get(
        path: '/teams/{team_id}/team-members/{id}',
        name: 'show_team_team_member',
        requirements: [
            'team_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'team_id',
        description: 'The ID of the team',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team member',
    )]
    #[ParamConverter(data: ['name' => 'teamMember'], class: TeamMember::class)]
    #[Resp\ObjectResponse(
        description: 'Shows the specific team member',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
    )]
    public function show(
        TeamMember $teamMember
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $teamMember);

        return $this->object($teamMember, groups: TeamMemberGroups::SHOW);
    }

    #[Rest\Post(
        path: '/teams/{id}/team-members',
        name: 'store_team_team_member',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team',
    )]
    #[Param\Instance(TeamMember::class, TeamMemberGroups::CREATE)]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\ObjectResponse(
        description: 'Creates a new member for the team',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        Team         $team,
        Request      $request
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

        /** @var TeamMember $teamMember */
        $teamMember = $instantiator->deserialize(
            $request->getContent(),
            TeamMember::class,
            TeamMemberGroups::CREATE
        );

        $this->invitationService->invite($team, $teamMember);

        return $this->object(
            $teamMember,
            201,
            TeamMemberGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/teams/{team_id}/team-members/{id}',
        name: 'update_team_team_member',
        requirements: [
            'team_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'team_id',
        description: 'The ID of the team',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team member',
    )]
    #[Param\Instance(TeamMember::class, TeamMemberGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class, options: ['id' => 'team_id'])]
    #[ParamConverter(data: ['name' => 'teamMember'], class: TeamMember::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific member for the team',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
    )]
    public function update(
        Instantiator           $instantiator,
        Request                $request,
        EntityManagerInterface $manager,
        Team                   $team,
        TeamMember             $teamMember
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);
        $teamMember = $instantiator->deserialize(
            $request->getContent(),
            TeamMember::class,
            TeamMemberGroups::UPDATE,
            $teamMember
        );

        $manager->persist($teamMember);
        $manager->flush();

        return $this->object($teamMember, groups: TeamMemberGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/teams/{team_id}/team-members/{id}',
        name: 'remove_team_team_member',
        requirements: [
            'team_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'team_id',
        description: 'The ID of the team',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team member',
    )]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class, options: ['id' => 'team_id'])]
    #[ParamConverter(data: ['name' => 'teamMember'], class: TeamMember::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific member from the team',
    )]
    public function remove(
        EntityManagerInterface $manager,
        Team                   $team,
        TeamMember             $teamMember
    ): Response
    {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);
        $manager->remove($teamMember);
        $manager->flush();

        return $this->empty();
    }

    #[Rest\Get(
        path: '/team-invites',
        name: 'invites_team_members',
    )]
    #[Tag('Team')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of invites for a user',
        class: TeamMember::class,
        group: TeamMemberGroups::INDEX,
    )]
    public function invites(
        ParamFetcherInterface $paramFetcher,
        TeamMemberRepository  $repository
    ): Response
    {
        $list = $repository->invitesByEmail($this->getUser()->getEmail());

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: [TeamMemberGroups::INDEX, TeamGroups::INDEX]);
    }

    #[Rest\Post(
        path: '/team-invites/{id}/accept',
        name: 'accept_team_members',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team member',
    )]
    #[Param\Instance(TeamMember::class, TeamMemberGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'teamMember'], class: TeamMember::class)]
    #[Resp\ObjectResponse(
        description: 'Accepts a team invite',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
        status: 201,
    )]
    public function acceptInvite(
        TeamMember $teamMember,
    ): Response
    {
        $this->invitationService->accept($teamMember, $this->getUser());

        return $this->object(
            $teamMember,
            201,
            TeamMemberGroups::SHOW
        );
    }
}