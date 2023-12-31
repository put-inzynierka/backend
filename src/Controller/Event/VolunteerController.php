<?php

namespace App\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\Volunteer;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use App\Helper\Paginator;
use App\Repository\VolunteerRepository;
use App\Service\Validation\TimeframeValidator;
use App\Voter\Qualifier;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VolunteerController extends AbstractController
{
    #[Rest\Get(
        path: '/events/{id}/volunteers',
        name: 'index_event_volunteers',
        requirements: ['id' => '\d+'],
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the event',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of volunteers for the event',
        class: Volunteer::class,
        group: VolunteerGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        VolunteerRepository $repository,
        Event $event
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $list = $repository->indexByEvent($event);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: VolunteerGroups::INDEX);
    }

    #[Rest\Post(
        path: '/events/{id}/volunteers',
        name: 'store_event_volunteer',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the event',
    )]
    #[Param\Instance(Volunteer::class, VolunteerGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new volunteer for the event',
        class: Volunteer::class,
        group: VolunteerGroups::INDEX,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        EntityManagerInterface $manager,
        Request $request,
        TimeframeValidator $timeframeValidator,
        Event $event,
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Volunteer $volunteer */
        $volunteer = $instantiator->deserialize(
            $request->getContent(),
            Volunteer::class,
            VolunteerGroups::CREATE
        );

        $volunteer
            ->setEvent($event)
            ->setUser($this->getUser())
        ;
        
        try {
            $manager->beginTransaction();
            
            $manager->persist($volunteer);
            $manager->flush();

            $instantiator->validateContainment($volunteer);
        } catch (\Throwable $th) {
            $manager->rollback();

            throw $th;
        }

        $manager->commit();

        return $this->object(
            $volunteer,
            201,
            VolunteerGroups::INDEX
        );
    }
}
