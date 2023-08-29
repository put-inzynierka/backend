<?php

namespace App\Controller\Event;

use App\Component\Attribute\Response as Resp;
use App\Component\Model\AvailableStand\Event as EventModel;
use App\Controller\AbstractController;
use App\Entity\Event\Event;
use App\Enum\SerializationGroup\BaseGroups;
use App\Helper\Paginator;
use App\Service\Stand\AvailabilityService;
use App\Voter\Qualifier;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Response;

class StandController extends AbstractController
{
    #[Rest\Get(path: '/events/{id}/available-stands', name: 'index_events_available_stands')]
    #[Tag('Reservation')]
    #[Resp\PageResponse(
        description: 'Returns the list of available stands for reservations autocomplete',
        class: EventModel::class,
        group: BaseGroups::DEFAULT,
    )]
    public function indexAvailable(
        AvailabilityService $availabilityService,
        Event $event
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        $result = $availabilityService->buildReservationAutocomplete($event);
        $page = Paginator::wrapArray($result);

        return $this->object($page, groups: BaseGroups::DEFAULT);
    }
}
