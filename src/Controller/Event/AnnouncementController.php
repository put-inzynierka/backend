<?php declare(strict_types=1);

namespace App\Controller\Event;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Event\Announcement;
use App\Entity\Event\Event;
use App\Enum\SerializationGroup\BaseGroups;
use App\Enum\SerializationGroup\Event\AnnouncementGroups;
use App\Helper\Paginator;
use App\Repository\AnnouncementRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

final class AnnouncementController extends AbstractController
{
    #[Rest\Get(
        path: '/events/{id}/announcements',
        name: 'index_event_announcements',
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
        description: 'Returns the list announcements for the event',
        class: Announcement::class,
        group: AnnouncementGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface  $paramFetcher,
        AnnouncementRepository $repository,
        Event                  $event
    ): Response {
        $list = $repository->indexByEvent($event);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: [AnnouncementGroups::INDEX, BaseGroups::TIMESTAMPS]);
    }

    #[Rest\Get(
        path: '/events/{event_id}/announcements/{id}',
        name: 'show_event_announcement',
        requirements: [
            'event_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'event_id',
        description: 'The ID of the event',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the announcement',
    )]
    #[Resp\ObjectResponse(
        description: 'Shows the specific announcement',
        class: Announcement::class,
        group: AnnouncementGroups::SHOW,
    )]
    public function show(
        #[MapEntity(expr: 'repository.findWithParent(id, event_id)')]
        Announcement $announcement
    ): Response {
        return $this->object($announcement, groups: [AnnouncementGroups::SHOW, BaseGroups::TIMESTAMPS]);
    }

    #[Rest\Post(
        path: '/events/{id}/announcements',
        name: 'store_event_announcement',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the event',
    )]
    #[Param\Instance(Announcement::class, AnnouncementGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new announcement for the event',
        class: Announcement::class,
        group: AnnouncementGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        EntityManagerInterface $manager,
        Event $event,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        /** @var Announcement $announcement */
        $announcement = $instantiator->deserialize(
            $request->getContent(),
            Announcement::class,
            AnnouncementGroups::CREATE
        );
        $announcement->setEvent($event);

        $manager->persist($announcement);
        $manager->flush();

        return $this->object(
            $announcement,
            201,
            AnnouncementGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/events/{event_id}/announcements/{id}',
        name: 'update_event_announcement',
        requirements: [
            'event_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'event_id',
        description: 'The ID of the event',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the announcement',
    )]
    #[Param\Instance(Announcement::class, AnnouncementGroups::UPDATE)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific announcement for the event',
        class: Announcement::class,
        group: AnnouncementGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        #[MapEntity(expr: 'repository.findWithParent(id, event_id)')]
        Announcement $announcement
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $announcement = $instantiator->deserialize(
            $request->getContent(),
            Announcement::class,
            AnnouncementGroups::UPDATE,
            $announcement
        );

        $manager->persist($announcement);
        $manager->flush();

        return $this->object($announcement, groups: AnnouncementGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/events/{event_id}/announcements/{id}',
        name: 'remove_event_announcement',
        requirements: [
            'event_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'event_id',
        description: 'The ID of the event',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the announcement',
    )]
    #[Resp\EmptyResponse(
        description: 'Removes the specific announcement from the event',
    )]
    public function remove(
        EntityManagerInterface $manager,
        #[MapEntity(expr: 'repository.findWithParent(id, event_id)')]
        Announcement $announcement
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $manager->remove($announcement);
        $manager->flush();

        return $this->empty();
    }
}