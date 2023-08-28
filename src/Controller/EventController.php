<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Event\Event;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class EventController extends AbstractController
{
    #[Rest\Get(path: '/events', name: 'index_events')]
    #[Tag('Event')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of events',
        class: Event::class,
        group: EventGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Event::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: EventGroups::INDEX);
    }

    #[Rest\Get(
        path: '/events/{id}',
        name: 'show_event',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path('id', description: 'The ID of the event')]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific event',
        class: Event::class,
        group: EventGroups::SHOW,
    )]
    public function show(
        Event $event
    ): Response {
        return $this->object($event, groups: EventGroups::SHOW);
    }

    #[Rest\Post(path: '/events', name: 'store_event')]
    #[Tag('Event')]
    #[Param\Instance(Event::class, EventGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new event',
        class: Event::class,
        group: EventGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        /** @var Event $event */
        $event = $instantiator->deserialize(
            $request->getContent(),
            Event::class,
            EventGroups::CREATE
        );

        $manager->persist($event);
        $manager->flush();

        return $this->object(
            $event,
            201,
            EventGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/events/{id}',
        name: 'update_event',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path('id', description: 'The ID of the event')]
    #[Param\Instance(Event::class, EventGroups::UPDATE)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific event',
        class: Event::class,
        group: EventGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        Event $event
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $event = $instantiator->deserialize(
            $request->getContent(),
            Event::class,
            EventGroups::UPDATE,
            $event
        );

        $manager->persist($event);
        $manager->flush();

        return $this->object($event, groups: EventGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/events/{id}',
        name: 'remove_event',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path('id', description: 'The ID of the event')]
    #[Resp\EmptyResponse(
        description: 'Removes the specific event',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Event $event
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $manager->remove($event);
        $manager->flush();

        return $this->empty();
    }
}
