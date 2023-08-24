<?php

namespace App\Controller\Event\Volunteer;

use App\Component\Model\Message;
use App\Entity\Event\Event;
use App\Entity\Event\Volunteer;
use App\Entity\Mail\MailLog;
use App\Enum\SerializationGroup\BaseGroups;
use App\Enum\SerializationGroup\MessageGroups;
use App\Service\Mail\Mailer;
use App\Voter\Qualifier;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Service\Instantiator;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    #[Rest\Post(
        path: '/events/{eventId}/volunteers/{volunteerId}/messages',
        name: 'store_event_volunteer_message',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Event')]
    #[Param\Path(
        name: 'eventId',
        description: 'The ID of the event',
    )]
    #[Param\Path(
        name: 'volunteerId',
        description: 'The ID of the volunteer',
    )]
    #[Param\Instance(Message::class, MessageGroups::CREATE)]
    #[ParamConverter(data: ['name' => 'event'], class: Event::class)]
    #[ParamConverter(data: ['name' => 'volunteer'], class: Volunteer::class)]
    #[Resp\ObjectResponse(
        description: 'Messages a volunteer',
        class: MailLog::class,
        group: BaseGroups::DEFAULT,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        Mailer $mailer,
        Event $event,
        Volunteer $volunteer,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        /** @var Message $message */
        $message = $instantiator->deserialize(
            $request->getContent(),
            Message::class,
            MessageGroups::CREATE
        );

        $mailLog = $mailer->send(
            $volunteer->getUser()->getEmail(),
            $message->getTitle(),
            $message->getMessage(),
        );

        return $this->object($mailLog, 201);
    }
}