<?php declare(strict_types=1);

namespace App\Controller\Team;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Project\Project;
use App\Entity\Team\Team;
use App\Enum\SerializationGroup\Project\ProjectGroups;
use App\Helper\Paginator;
use App\Repository\ProjectRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Response;

final class ProjectController extends AbstractController
{
    #[Rest\Get(
        path: '/teams/{id}/projects',
        name: 'index_team_projects',
    )]
    #[Tag('Team')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of projects for a given team',
        class: Project::class,
        group: ProjectGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        ProjectRepository     $repository,
        Team                  $team
    ): Response {
        $list = $repository->indexByTeam($team);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: ProjectGroups::INDEX);
    }
}