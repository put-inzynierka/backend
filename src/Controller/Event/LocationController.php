<?php declare(strict_types=1);

namespace App\Controller\Event;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Event\Day;
use App\Entity\Location\Location;
use App\Enum\SerializationGroup\Location\LocationGroups;
use App\Helper\Paginator;
use App\Repository\LocationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;
final class LocationController extends AbstractController
{
    #[Rest\Get(
        path: '/event/{id}/day/{day_id}/reserved-locations',
        name: 'event_reserved_locations',
        requirements: [
            'day_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'day_id',
        description: 'The ID of the day',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the event',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of locations with reservations for event day',
        class: Location::class,
        group: LocationGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        LocationRepository $locationRepository,
        #[MapEntity(expr: 'repository.findWithParent(day_id, id)')]
        Day $day
    ): Response {
        $list = $locationRepository->indexReservedByDay($day);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: LocationGroups::INDEX);
    }
}