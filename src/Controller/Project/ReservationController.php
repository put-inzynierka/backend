<?php

namespace App\Controller\Project;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Project\Project;
use App\Entity\Project\Reservation;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Helper\Paginator;
use App\Repository\ReservationRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class ReservationController extends AbstractController
{
    #[Rest\Get(
        path: '/projects/{id}/reservations',
        name: 'index_project_reservations',
        requirements: ['id' => '\d+'],
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list stands for the location',
        class: Reservation::class,
        group: ReservationGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        ReservationRepository $repository,
        Project               $project
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $project->getTeam());

        $list = $repository->indexByProject($project);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: ReservationGroups::INDEX);
    }

    #[Rest\Post(
        path: '/projects/{id}/reservations',
        name: 'store_project_reservation',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Param\Instance(Reservation::class, ReservationGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new reservation for the project',
        class: Reservation::class,
        group: ReservationGroups::CREATE,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        EntityManagerInterface $manager,
        Request $request,
        Project $project
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        /** @var Reservation $reservation */
        $reservation = $instantiator->deserialize(
            $request->getContent(),
            Reservation::class,
            ReservationGroups::CREATE
        );

        $reservation
            ->setProject($project)
            ->setConfirmed(null)
        ;

        try {
            $manager->beginTransaction();

            $manager->persist($reservation);
            $manager->flush();

            $instantiator->validateContainment($reservation);
        } catch (\Throwable $th) {
            $manager->rollback();

            throw $th;
        }

        $manager->commit();

        return $this->object(
            $reservation,
            201,
            ReservationGroups::CREATE
        );
    }

    #[Rest\Patch(
        path: '/projects/{project_id}/reservations/{id}',
        name: 'update_project_reservation',
        requirements: [
            'project_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'project_id',
        description: 'The ID of the project',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the reservation',
    )]
    #[Param\Instance(Reservation::class, ReservationGroups::UPDATE)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific reservation for project',
        class: Reservation::class,
        group: ReservationGroups::UPDATE,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        #[MapEntity(expr: 'repository.findWithParent(id, project_id)')]
        Reservation $reservation
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $reservation->getProject()->getTeam());

        /** @var Reservation $reservation */
        $reservation = $instantiator->deserialize(
            $request->getContent(),
            Reservation::class,
             ReservationGroups::UPDATE,
            $reservation
        );

        try {
            $manager->beginTransaction();

            $manager->persist($reservation);
            $manager->flush();

            $instantiator->validateContainment($reservation);
        } catch (\Throwable $th) {
            $manager->rollback();

            throw $th;
        }

        $manager->commit();

        return $this->object($reservation, groups: ReservationGroups::UPDATE);
    }

    #[Rest\Delete(
        path: '/projects/{project_id}/reservations/{id}',
        name: 'remove_project_reservation',
        requirements: [
            'project_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'project_id',
        description: 'The ID of the project',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the reservation',
    )]
    #[Resp\EmptyResponse(
        description: 'Removes the specific reservation from the project',
    )]
    public function remove(
        EntityManagerInterface $manager,
        #[MapEntity(expr: 'repository.findWithParent(id, project_id)')]
        Reservation $reservation
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $reservation->getProject()->getTeam());

        $manager->remove($reservation);
        $manager->flush();

        return $this->empty();
    }
}
