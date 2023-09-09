<?php declare(strict_types=1);

namespace App\Controller\Event;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Event\Day;
use App\Entity\Location\Location;
use App\Entity\Project\Reservation;
use App\Enum\SerializationGroup\Location\LocationGroups;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Helper\Paginator;
use App\Repository\LocationRepository;
use App\Repository\ReservationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

final class ReservationController extends AbstractController
{
    #[Rest\Get(
        path: '/events/{event_id}/days/{id}/reserved-locations',
        name: 'index_event_day_reserved_locations',
        requirements: [
            'event_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the day',
    )]
    #[Param\Path(
        name: 'event_id',
        description: 'The ID of the event',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of locations with reservations for event day',
        class: Location::class,
        group: LocationGroups::INDEX,
    )]
    public function reservedLocations(
        ParamFetcherInterface $paramFetcher,
        LocationRepository $locationRepository,
        #[MapEntity(expr: 'repository.findWithParent(id, event_id)')]
        Day $day
    ): Response {
        $list = $locationRepository->indexReservedByDay((string) $day->getId());

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: LocationGroups::INDEX);
    }

    #[Rest\Get(
        path: '/events/{event_id}/days/{day_id}/reserved-locations/{id}/reservations',
        name: 'index_event_day_reservations',
        requirements: [
            'event_id' => '\d+',
            'day_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the location',
    )]
    #[Param\Path(
        name: 'day_id',
        description: 'The ID of the day',
    )]
    #[Param\Path(
        name: 'event_id',
        description: 'The ID of the event',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of reservation by location for a given day',
        class: Reservation::class,
        group: ReservationGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        ReservationRepository $reservationRepository,
        #[MapEntity(expr: 'repository.findWithParent(day_id, event_id)')]
        Day $day,
        #[MapEntity(expr: 'repository.findWithParentByDay(id, event_id, day_id)')]
        Location $location
    ): Response {
        $list = $reservationRepository->indexByLocationAndDay($location, $day);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: ReservationGroups::INDEX);
    }
}