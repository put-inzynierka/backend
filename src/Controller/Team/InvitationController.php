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
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class InvitationController extends AbstractController
{
    public function __construct(
        private readonly TeamInvitationService $invitationService
    ) {
    }

    #[Rest\Post(
        path: '/teams/{id}/invite',
        name: 'invite_team_member',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team',
    )]
    #[Param\Instance(TeamMember::class, TeamMemberGroups::INVITE)]
    #[ParamConverter(data: ['name' => 'team'], class: Team::class)]
    #[Resp\ObjectResponse(
        description: 'Creates a new member for the team',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
        status: 201,
    )]
    public function invite(
        Instantiator $instantiator,
        Team         $team,
        Request      $request
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

        /** @var TeamMember $teamMember */
        $teamMember = $instantiator->deserialize(
            $request->getContent(),
            TeamMember::class,
            TeamMemberGroups::INVITE
        );

        $this->invitationService->invite($team, $teamMember);

        return $this->object(
            $teamMember,
            201,
            TeamMemberGroups::SHOW
        );
    }

    #[Rest\Get(
        path: '/team-invitations',
        name: 'index_team_invitations',
    )]
    #[Tag('Team')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of invites for a user',
        class: TeamMember::class,
        group: TeamMemberGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        TeamMemberRepository  $repository
    ): Response {
        $list = $repository->invitationsByEmail($this->getUser()->getEmail());

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: [TeamMemberGroups::INDEX, TeamGroups::INDEX]);
    }

    #[Rest\Post(
        path: '/team-invitations/{id}/accept',
        name: 'accept_team_invitation',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Team')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the team member',
    )]
    #[ParamConverter(data: ['name' => 'teamMember'], class: TeamMember::class)]
    #[Resp\ObjectResponse(
        description: 'Accepts a team invite',
        class: TeamMember::class,
        group: TeamMemberGroups::SHOW,
        status: 201,
    )]
    public function accept(
        TeamMember $teamMember,
    ): Response {
        $this->invitationService->accept($teamMember, $this->getUser());

        return $this->object(
            $teamMember,
            201,
            TeamMemberGroups::SHOW
        );
    }
}