<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Project\Project;
use App\Enum\SerializationGroup\Project\ProjectGroups;
use App\Helper\Paginator;
use App\Repository\ProjectRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProjectController extends AbstractController
{
    #[Rest\Get(
        path: '/projects',
        name: 'index_projects',
    )]
    #[Tag('Project')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of projects',
        class: Project::class,
        group: ProjectGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        ProjectRepository     $repository
    ): Response {
        $list = $repository->indexByUser($this->getUser());

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: ProjectGroups::INDEX);
    }

    #[Rest\Get(
        path: '/projects/{id}',
        name: 'show_project',
        requirements: [
            'id' => '\d+'
        ]
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Resp\ObjectResponse(
        description: 'Shows the specific project',
        class: Project::class,
        group: ProjectGroups::SHOW,
    )]
    public function show(
        Project $project
    ): Response {
        return $this->object($project, groups: ProjectGroups::SHOW);
    }

    #[Rest\Post(path: '/projects', name: 'store_project')]
    #[Tag('Project')]
    #[Param\Instance(Project::class, ProjectGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new project',
        class: Project::class,
        group: ProjectGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $project = $instantiator->deserialize(
            $request->getContent(),
            Project::class,
            ProjectGroups::CREATE
        );

        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        $manager->persist($project);
        $manager->flush();

        return $this->object(
            $project,
            201,
            ProjectGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/projects/{id}',
        name: 'update_project',
        requirements: [
            'id' => '\d+'
        ]
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Param\Instance(Project::class, ProjectGroups::UPDATE)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific project',
        class: Project::class,
        group: ProjectGroups::SHOW,
    )]
    public function update(
        Instantiator           $instantiator,
        Request                $request,
        EntityManagerInterface $manager,
        Project                $project
    ): Response {
        $project = $instantiator->deserialize(
            $request->getContent(),
            Project::class,
            ProjectGroups::UPDATE,
            $project
        );

        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        $manager->persist($project);
        $manager->flush();

        return $this->object($project, groups: ProjectGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/projects/{id}',
        name: 'remove_project',
        requirements: [
            'id' => '\d+'
        ]
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Resp\EmptyResponse(
        description: 'Removes the specific project',
    )]
    public function remove(
        EntityManagerInterface $manager,
        Project                $project
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        $manager->remove($project);
        $manager->flush();

        return $this->empty();
    }
}