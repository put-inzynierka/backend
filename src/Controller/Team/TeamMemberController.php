<?php declare(strict_types=1);

namespace App\Controller\Team;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Enum\SerializationGroup\Team\TeamMemberGroups;
use App\Helper\Paginator;
use App\Repository\TeamMemberRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

final class TeamMemberController extends AbstractController
{
    #[Rest\Get(
        path: '/teams/{id}/members',
        name: 'index_team_members',
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
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $team);

        $list = $repository->indexByTeam($team);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: TeamMemberGroups::INDEX);
    }

    #[Rest\Get(
        path: '/teams/{team_id}/members/{id}',
        name: 'show_team_member',
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
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $teamMember->getTeam());

        return $this->object($teamMember, groups: TeamMemberGroups::SHOW);
    }

    #[Rest\Patch(
        path: '/teams/{team_id}/members/{id}',
        name: 'update_team_member',
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
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

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
        path: '/teams/{team_id}/members/{id}',
        name: 'remove_team_member',
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
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $team);

        $manager->remove($teamMember);
        $manager->flush();

        return $this->empty();
    }
}