<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Component\Model\AvailableStand\Event as EventModel;
use App\Entity\Event\Event;
use App\Enum\SerializationGroup\BaseGroups;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Repository\SecurityUserRepository;
use App\Service\Stand\AvailabilityService;
use App\Voter\Qualifier;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Response;

class StandController extends AbstractController
{
    #[Rest\Get(path: '/available-stands', name: 'index_available_stands')]
    #[Tag('Reservation')]
    #[Resp\PageResponse(
        description: 'Returns the list of available stands for reservations autocomplete',
        class: EventModel::class,
        group: BaseGroups::DEFAULT,
    )]
    public function indexAvailable(
        RepositoryFactory $repositoryFactory,
        AvailabilityService $availabilityService
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        $repository = $repositoryFactory->create(Event::class);
        $list = $repository->index()->getQuery()->getResult();

        $result = $availabilityService->buildReservationAutocomplete($list, true);
        $page = Paginator::wrapArray($result);

        return $this->object($page, groups: BaseGroups::DEFAULT);
    }
}
