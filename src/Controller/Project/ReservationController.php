<?php

namespace App\Controller\Project;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Project\Project;
use App\Entity\Project\Reservation;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Exception\UnprocessableEntityHttpException;
use App\Service\Instantiator;
use App\Service\Validation\StandValidator;
use App\Service\Validation\TimeframeValidator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class ReservationController extends AbstractController
{
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
    #[ParamConverter(data: ['name' => 'project'], class: Project::class)]
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
            ->setConfirmed(false)
        ;

        $instantiator->validateContainment($reservation);

        $manager->persist($reservation);
        $manager->flush();

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
    #[ParamConverter(data: ['name' => 'project'], class: Project::class, options: ['id' => 'project_id'])]
    #[ParamConverter(data: ['name' => 'reservation'], class: Reservation::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific reservation for project',
        class: Reservation::class,
        group: ReservationGroups::UPDATE,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        TimeframeValidator $timeframeValidator,
        StandValidator $standValidator,
        Project $project,
        Reservation $reservation
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        /** @var Reservation $reservation */
        $reservation = $instantiator->deserialize(
            $request->getContent(),
            Reservation::class,
             ReservationGroups::UPDATE,
            $reservation
        );

        $instantiator->validateContainment($reservation);

        $manager->persist($reservation);
        $manager->flush();

        return $this->object($reservation, groups: ReservationGroups::UPDATE);
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
    #[ParamConverter(data: ['name' => 'project'], class: Project::class, options: ['id' => 'project_id'])]
    #[ParamConverter(data: ['name' => 'reservation'], class: Reservation::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific reservation from the project',
    )]
    public function remove(
        EntityManagerInterface $manager,
        Project $project,
        Reservation $reservation
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $project->getTeam());

        $manager->remove($reservation);
        $manager->flush();

        return $this->empty();
    }
}
