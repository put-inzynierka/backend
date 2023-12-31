<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Project\Reservation;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Helper\Paginator;
use App\Repository\ReservationRepository;
use App\Service\Instantiator;
use App\Service\Stand\AvailabilityService;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends AbstractController
{
    #[Rest\Get(path: '/reservations', name: 'index_reservations')]
    #[Tag('Reservation')]
    #[Param\Limit]
    #[Param\Page]
    #[Param\QueryParam('confirmed')]
    #[Resp\PageResponse(
        description: 'Returns the list of reservations',
        class: Reservation::class,
        group: ReservationGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        ReservationRepository $repository
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: ReservationGroups::INDEX);
    }

    #[Rest\Patch(
        path: '/reservations/{id}',
        name: 'update_reservation',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Reservation')]
    #[Param\Path('id', description: 'The ID of the reservation')]
    #[Param\Instance(Reservation::class, ReservationGroups::ADMIN_UPDATE)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific reservation',
        class: Reservation::class,
        group: ReservationGroups::ADMIN_UPDATE,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        AvailabilityService $availabilityService,
        Reservation $reservation
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        /** @var Reservation $reservation */
        $reservation = $instantiator->deserialize(
            $request->getContent(),
            Reservation::class,
            ReservationGroups::ADMIN_UPDATE,
            $reservation
        );

        $manager->persist($reservation);
        $manager->flush();

        $availabilityService->rebuild($reservation->getEvent());

        return $this->object($reservation, groups: ReservationGroups::ADMIN_UPDATE);
    }
}
